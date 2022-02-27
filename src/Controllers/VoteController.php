<?php

namespace Azuriom\Plugin\Vote\Controllers;

use Azuriom\Http\Controllers\Controller;
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('vote::index', [
            'user' => $request->user(),
            'request' => $request,
            'sites' => Site::enabled()->with('rewards')->get(),
            'rewards' => Reward::orderByDesc('chances')->get(),
            'votes' => Vote::getTopVoters(now()->startOfMonth()),
            'ipv6compatibility' => setting('vote.ipv4-v6-compatibility', true),
        ]);
    }

    public function verifyUser(Request $request, string $name)
    {
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

        $next = now()->addMinutes($site->vote_delay);
        Cache::put('votes.site.'.$site->id.'.'.$request->ip(), $next, $next);

        $reward = $site->getRandomReward();

        if ($reward !== null) {
            $site->votes()->create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
            ]);

            $reward->giveTo($user);
        }

        return response()->json(['message' => trans('vote::messages.success')]);
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
}
