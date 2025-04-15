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
        ]);
    }

    public function verifyUser(Request $request, string $name)
    {
        if (setting('vote.auth_required', false)) {
            return response()->json([
                'message' => trans('vote::messages.errors.auth'),
            ], 422);
        }

        $user = User::firstWhere('name', $name);

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

        return response()->json([
            'sites' => $sites,
            'votes' => $this->getVotesCount($user),
        ]);
    }

    public function vote()
    {
        return response()->noContent(404);
    }

    public function done(Request $request, Site $site)
    {
        $user = $request->user() ?? User::firstWhere('name', $request->input('user'));

        abort_if($user === null, 401);

        $nextVoteTime = $site->getNextVoteTime($user, $request->ip());

        if ($nextVoteTime !== null) {
            return response()->json([
                'message' => $this->formatTimeMessage($nextVoteTime),
            ], 422);
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

        // Check again because sometimes API can be really slow...
        $nextVoteTime = $site->getNextVoteTime($user, $request->ip());

        if ($nextVoteTime !== null) {
            return response()->json([
                'message' => $this->formatTimeMessage($nextVoteTime),
            ], 422);
        }

        $reward = $site->getRandomReward();

        if ($reward !== null) {
            if ($reward->single_server) {
                $request->session()->put('vote.reward.'.$site->id, $reward->id);

                return response()->json([
                    'status' => 'select_server',
                    'servers' => $reward->servers->pluck('name', 'id'),
                ]);
            }

            $vote = $site->votes()->create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
            ]);

            $reward->dispatch($vote);
        }

        $next = $site->vote_reset_at !== null
            ? now()->next($site->vote_reset_at)
            : now()->addMinutes($site->vote_delay);
        Cache::put('votes.site.'.$site->id.'.'.$request->ip(), $next, $next);

        return response()->json([
            'message' => trans('vote::messages.success', [
                'reward' => $reward?->name ?? trans('messages.unknown'),
            ]),
        ]);
    }

    private function selectServer(Request $request, User $user, Site $site, Reward $reward)
    {
        $server = Server::find($request->input('server'));

        if ($server === null || ! $reward->servers->contains($server)) {
            return response()->json([
                'status' => 'select_server',
                'servers' => $reward->servers->pluck('name', 'id'),
            ]);
        }

        $request->session()->forget('vote.reward.'.$site->id);

        $vote = $site->votes()->create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
        ]);

        $reward->dispatch($vote, [$server]);

        return response()->json([
            'message' => trans('vote::messages.success', [
                'reward' => $reward?->name ?? trans('messages.unknown'),
            ]),
        ]);
    }

    private function formatTimeMessage(Carbon $nextVoteTime)
    {
        $time = $nextVoteTime->diffForHumans([
            'parts' => 2,
            'join' => true,
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
        ]);

        return trans('vote::messages.errors.delay', ['time' => $time]);
    }

    private function getVotesCount(User $user)
    {
        return Vote::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }
}
