<?php

namespace Azuriom\Plugin\Vote\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\Vote\Models\Reward;
use Azuriom\Plugin\Vote\Models\Site;
use Azuriom\Plugin\Vote\Models\Vote;
use Azuriom\Plugin\Vote\Verification\VoteChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    /**
     * Display the vote home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $votes = DB::table((new Vote())->getTable())
            ->select(['user_id', DB::raw('COUNT(user_id) AS count')])
            ->where('created_at', '>', now()->startOfMonth())
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->take(setting('vote.top-players-count', 10))
            ->get();

        $users = User::findMany($votes->pluck('user_id'))->keyBy('id');

        $votes = $votes->mapWithKeys(function ($vote, $position) use ($users) {
            return [
                $position + 1 => [
                    'user' => $users->get($vote->user_id),
                    'votes' => $vote->count,
                ],
            ];
        });

        return view('vote::index', [
            'sites' => Site::whereHas('rewards')->get(),
            'rewards' => Reward::orderByDesc('chances')->get(),
            'votes' => $votes,
        ]);
    }

    public function verifyUser(string $name)
    {
        if (! User::where('name', $name)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('vote::messages.unknown-user'),
            ], 422);
        }

        return response()->json(['status' => 'success']);
    }

    public function canVote(Request $request, Site $site)
    {
        $user = $request->user() ?? User::firstWhere('name', $request->input('user'));

        if ($user === null) {
            abort(401);
        }

        $nextVoteTime = $this->getNextVoteTime($site, $user);

        if ($nextVoteTime !== null) {
            return response()->json([
                'status' => 'error',
                'message' => trans('vote::messages.vote-delay', ['time' => $nextVoteTime]),
            ], 422);
        }

        if ($site->rewards->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('vote::messages.site-no-rewards'),
            ], 422);
        }

        return response()->json(['status' => 'success']);
    }

    public function done(Request $request, Site $site)
    {
        $user = $request->user() ?? User::firstWhere('name', $request->input('user'));

        if ($user === null) {
            abort(401);
        }

        $nextVoteTime = $this->getNextVoteTime($site, $user);

        if ($nextVoteTime !== null) {
            return response()->json([
                'status' => 'error',
                'message' => trans('vote::messages.vote-delay', ['time' => $nextVoteTime]),
            ], 422);
        }

        if ($site->rewards->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('vote::messages.site-no-rewards'),
            ], 422);
        }

        if (! app(VoteChecker::class)->verifyVote($site->url, $request->ip(), $user->name)) {
            return response()->json([
                'status' => 'pending',
            ]);
        }

        $reward = $this->getRandomReward($site);

        $site->votes()->create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
        ]);

        $commands = array_map(function ($el) use ($reward) {
            return str_replace('{reward}', $reward->name, $el);
        }, $reward->commands ?? []);

        $reward->server->bridge()->executeCommands($commands, $user->name, $reward->need_online);

        return response()->json([
            'status' => 'success',
            'message' => trans('vote::messages.vote-success'),
        ]);
    }

    private function getNextVoteTime(Site $site, User $user)
    {
        $lastVote = $site->votes()
            ->where('user_id', $user->id)
            ->where('created_at', '>', now()->subMinutes($site->vote_delay))
            ->latest()
            ->first();

        if ($lastVote === null) {
            return null;
        }

        return $lastVote->created_at->addMinutes($site->vote_delay)->diffForHumans();
    }

    private function getRandomReward(Site $site)
    {
        $rewards = $site->rewards;

        $total = $rewards->sum('chances');
        $random = random_int(0, $total);

        $sum = 0;

        foreach ($rewards as $reward) {
            $sum += $reward->chances;

            if ($sum >= $random) {
                return $reward;
            }
        }

        return $rewards->first();
    }
}
