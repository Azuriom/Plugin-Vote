<?php

return [
    'title' => 'Vote',

    'sections' => [
        'vote' => 'Vote',
        'top' => 'Top votes',
        'rewards' => 'Rewards',
    ],

    'fields' => [
        'server' => 'Server',
        'chances' => 'Chances',
        'rewards' => 'Rewards',
        'commands' => 'Commands',
        'votes' => 'Votes',
    ],

    'errors' => [
        'user' => 'This user don\'t exists !',
        'site' => 'No voting site is available currently.',
        'delay' => 'You already voted, you can vote again in :time !',
        'rewards' => 'This site has no rewards.',
    ],

    'success' => 'Your vote has been taken into account, you will receive your rewards soon!',
];
