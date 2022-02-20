<?php

use Azuriom\Plugin\Vote\Models\Vote;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
|
| Here is where you can register helpers for your plugin. These
| functions are loaded by Composer and are globally available on the app !
| Just make sure you verify that a function doesn't exist before registering it
| to prevent any side effect.
|
*/

if (! function_exists('display_rewards')) {
    function display_rewards()
    {
        return setting('vote.display-rewards', true);
    }
}

if (! function_exists('vote_leaderboard')) {
    function vote_leaderboard()
    {
        return Cache::remember('vote.leaderboard', now()->addMinutes(5), function () {
            return Vote::getTopVoters(now()->startOfMonth())->map(function ($value) {
                return (object) $value;
            });
        });
    }
}
