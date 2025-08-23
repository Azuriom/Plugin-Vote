<?php

namespace Azuriom\Plugin\Vote\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Vote\Models\Site;
use Azuriom\Plugin\Vote\Models\User;
use Azuriom\Plugin\Vote\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function index(Request $request)
    {
        $sites = Site::enabled()->get();
        $topVotes = Vote::getTopVoters(now()->startOfMonth())->values();
        $users = User::withCount([
            'votes' => fn ($q) => $q->where('created_at', '>=', now()->startOfMonth()),
        ])
            ->whereIn('name', explode(',', $request->input('usernames', '')))
            ->get()
            ->map(function (User $user) use ($sites, $topVotes) {
                $voteTimes = $sites->mapWithKeys(fn (Site $site) => [
                    $site->id => $site->getNextVoteTime($user)?->toIso8601String(),
                ]);
                $topVote = $topVotes->firstWhere('user.id', $user->id);

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'uid' => $user->game_id,
                    'votes' => $user->votes_count,
                    'sites' => $voteTimes,
                    'position' => $topVote !== null ? $topVote->position : -1,
                ];
            });

        $topVotes = $topVotes->map(fn (object $vote) => [
            'id' => $vote->user->id,
            'name' => $vote->user->name,
            'uid' => $vote->user->game_id,
            'votes' => $vote->votes,
        ]);

        return response()->json([
            'users' => $users,
            'top_votes' => $topVotes,
            'sites' => $sites->map(fn (Site $site) => [
                'id' => $site->id,
                'name' => $site->name,
                'url' => $site->url,
            ]),
        ]);
    }
}
