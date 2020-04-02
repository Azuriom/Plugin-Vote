<?php

return [
    'nav' => [
        'title' => 'Vote',

        'settings' => 'Settings',
        'sites' => 'Sites',
        'rewards' => 'Rewards',
    ],

    'settings' => [
        'title' => 'Vote page settings',

        'count' => 'Top Players Count',
        'display-rewards' => 'Show rewards in vote page',
    ],

    'sites' => [
        'title' => 'Sites',
        'title-edit' => 'Edit site :site',
        'title-create' => 'Create site',

        'enable' => 'Enable the site',

        'delay' => 'Delay between votes in minutes',

        'status' => [
            'created' => 'The site has been added.',
            'updated' => 'This site has been updated.',
            'deleted' => 'This site has been removed.',
        ],
    ],

    'rewards' => [
        'title' => 'Rewards',
        'title-edit' => 'Edit reward :reward',
        'title-create' => 'Create reward',

        'need-online' => 'The user must be online to receive the reward (only available with AzLink)',
        'enable' => 'Enable the reward',

        'commands-info' => 'You can use <code>{player}</code> to use the player name and <code>{reward}</code> to use the reward name.',

        'status' => [
            'created' => 'The reward has been created.',
            'updated' => 'This reward has been updated.',
            'deleted' => 'This reward has been deleted.',
        ],
    ],
];
