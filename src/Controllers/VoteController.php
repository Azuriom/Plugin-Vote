<?php

namespace Azuriom\Plugin\Vote\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Server;
use Azuriom\Models\User;
use Azuriom\Plugin\Vote\Models\Reward;
use Azuriom\Plugin\Vote\Models\Site;
use Azuriom\Plugin\Vote\Models\Vote;
use Azuriom\Plugin\Vote\Verification\VoteChecker;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VoteController extends Controller
{
    /**
     * Display the vote home page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $queryName = ($gameId = $request->input('uid')) !== null
            ? User::where('game_id', $gameId)->value('name')
            : $request->input('user', '');
        $votesCount = $user !== null ? $this->getVotesCount($user) : -1;
        $goalTarget = (int) setting('vote.goal.target', -1);
        $goalProgress = $goalTarget > 0 ? Vote::getGoalProgress() : 0;

        return view('vote::index', [
            'name' => $queryName,
            'user' => $request->user(),
            'request' => $request,
            'sites' => Site::enabled()->with('rewards')->get(),
            'rewards' => Reward::where('chances', '>', 0)->orderByDesc('chances')->get(),
            'votes' => Vote::getTopVoters(now()->startOfMonth()),
            'userVotes' => $votesCount,
            'ipv6compatibility' => setting('vote.ipv4-v6-compatibility', true),
            'authRequired' => setting('vote.auth-required', false),
            'displayRewards' => (bool) setting('vote.display-rewards', true),
            'goalEnabled' => $goalTarget > 0,
            'goalTarget' => $goalTarget,
            'goalPercentage' => $goalTarget > 0 ? round(($goalProgress / $goalTarget) * 100) : 0,
            'goalProgress' => $goalProgress,
        ]);
    }

    public function verifyUser(Request $request, string $name)
    {
        $user = $request->user();

        if ($user === null && setting('vote.auth-required', false)) {
            return response()->json([
                'message' => trans('vote::messages.errors.auth'),
            ], 401);
        }

        // Find user by name only if user is not currently authenticated.
        $user ??= User::firstWhere('name', $name);

        if ($user === null) {
            return response()->json([
                'message' => trans('vote::messages.errors.user'),
            ], 422);
        }

        $sites = Site::enabled()
            ->with('rewards')
            ->get()
            ->mapWithKeys(function (Site $site) use ($user, $request) {
                return [
                    $site->id => $site->getNextVoteTime($user, $request->ip())?->valueOf(),
                ];
            });

        $goalTarget = (int) setting('vote.goal.target', -1);
        $goalProgress = $goalTarget > 0 ? Vote::getGoalProgress() : 0;

        return response()->json([
            'sites' => $sites,
            'votes' => $this->getVotesCount($user),
            'goal' => [
                'target' => $goalTarget,
                'progress' => $goalProgress,
                'text' => trans('vote::messages.goal', ['current' => $goalProgress, 'target' => $goalTarget]),
            ],
        ]);
    }

    public function vote()
    {
        return response()->noContent(404);
    }

    public function done(Request $request, Site $site)
    {
        abort_unless($site->is_enabled, 404);

        $user = $request->user();

        if ($user === null && ! setting('vote.auth-required', false)) {
            $user = User::firstWhere('name', $request->input('user'));
        }

        abort_if($user === null, 401);

        $nextVoteTime = $site->getNextVoteTime($user, $request->ip());

        if ($nextVoteTime !== null) {
            return response()->json([
                'message' => $this->formatTimeMessage($nextVoteTime),
            ], 419);
        }

        $previousReward = $request->session()->has('vote.reward.'.$site->id)
            ? Reward::find($request->session()->get('vote.reward.'.$site->id))
            : null;

        if ($previousReward !== null) {
            return $this->selectServer($request, $user, $site, $previousReward);
        }

        $voteChecker = app(VoteChecker::class);

        if ($site->has_verification && ! $voteChecker->verifyVote($site, $user, $request->ip())) {
            return response()->json([
                'status' => 'pending',
            ]);
        }

        $reward = $site->getRandomReward();

        if ($reward?->single_server) {
            $request->session()->put('vote.reward.'.$site->id, $reward->id);

            return response()->json([
                'status' => 'select_server',
                'servers' => $reward->servers->pluck('name', 'id'),
            ]);
        }

        return $this->finalizeVote($request, $user, $site, $reward);
    }

    private function selectServer(Request $request, User $user, Site $site, Reward $reward): JsonResponse
    {
        $server = Server::find($request->input('server'));

        if ($server === null || ! $reward->servers->contains($server)) {
            return response()->json([
                'status' => 'select_server',
                'servers' => $reward->servers->pluck('name', 'id'),
            ]);
        }

        $request->session()->forget('vote.reward.'.$site->id);

        return $this->finalizeVote($request, $user, $site, $reward, $server);
    }

    private function finalizeVote(Request $request, User $user, Site $site, ?Reward $reward, ?Server $server = null)
    {
        $lockKey = "votes.site.{$site->id}.user.{$user->id}";

        try {
            // Release abandoned locks after 10 seconds and wait up to 5 seconds for another request to finish
            $result = Cache::lock($lockKey, 10)->block(5, function () use ($request, $user, $site, $reward) {
                $nextVoteTime = $site->getFreshNextVoteTime($user, $request->ip());

                if ($nextVoteTime !== null) {
                    return $nextVoteTime;
                }

                $vote = $reward !== null
                    ? $site->votes()->create(['user_id' => $user->id, 'reward_id' => $reward->id])
                    : null;

                $next = $site->vote_reset_at !== null
                    ? now()->next($site->vote_reset_at)
                    : now()->addMinutes($site->vote_delay);
                Cache::put("votes.site.{$site->id}.{$request->ip()}", $next, $next);

                return $vote;
            });
        } catch (LockTimeoutException) {
            return response()->json([
                'status' => 'pending',
            ]);
        }

        if ($result instanceof Carbon) {
            return response()->json([
                'message' => $this->formatTimeMessage($result),
            ], 419);
        }

        if ($result instanceof Vote && $reward !== null) {
            if ($server !== null) {
                $reward->dispatch($result, [$server]);
            } else {
                $reward->dispatch($result);
            }

            $this->processVoteGoal($user);
        }

        return response()->json([
            'message' => trans('vote::messages.success', [
                'reward' => $reward?->name ?? trans('messages.unknown'),
            ]),
        ]);
    }

    private function processVoteGoal(User $user): void
    {
        $goalTarget = (int) setting('vote.goal.target', -1);
        $goalCommands = setting('vote.goal.commands');

        if ($goalTarget <= 0 || ! $goalCommands) {
            return;
        }

        $commands = collect(json_decode($goalCommands, true));

        if ($commands->isEmpty() || Vote::getGoalProgress() !== $goalTarget) {
            return;
        }

        $servers = Server::findMany($commands->pluck('server')->unique());

        foreach ($servers as $server) {
            $serverCommands = $commands->where('server', $server->id)
                ->pluck('commands')
                ->flatten()
                ->all();

            $server->bridge()->sendCommands($serverCommands, $user);
        }
    }

    private function formatTimeMessage(Carbon $nextVoteTime): string
    {
        $time = $nextVoteTime->diffForHumans([
            'parts' => 2,
            'join' => true,
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
        ]);

        return trans('vote::messages.errors.delay', ['time' => $time]);
    }

    private function getVotesCount(User $user): int
    {
        return Vote::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }
}
