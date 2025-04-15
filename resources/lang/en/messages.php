<?php

return [
    'title' => 'Vote',

    'sections' => [
        'vote' => 'Vote',
        'top' => 'Top votes',
        'rewards' => 'Rewards',
    ],

    'fields' => [
        'chances' => 'Chances',
        'commands' => 'Commands',
        'reward' => 'Reward',
        'rewards' => 'Rewards',
        'servers' => 'Servers',
        'site' => 'Site',
        'votes' => 'Votes',
    ],

    'errors' => [
        'user' => 'This user doesn\'t exist.',
        'site' => 'No voting site is available currently.',
        'delay' => 'You already voted, you can vote again in :time.',
        'auth' => 'You must be logged in to vote.',
    ],

    'votes' => 'You have voted :count time this month.|You have voted :count times this month.',

    'server' => 'Choose the server on which to receive the reward.',

    'success' => 'Your vote has been taken into account, you will soon receive the reward ":reward"!',

    'notifications' => [
        'top' => 'Congratulations, you have received ":reward" for being the #:position top voter of the month!',
    ],
];
