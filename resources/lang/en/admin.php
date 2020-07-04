<?php

return [
    'nav' => [
        'title' => 'Vote',

        'settings' => 'Settings',
        'statistics' => 'Statistics',
        'sites' => 'Sites',
        'rewards' => 'Rewards',
    ],

    'permission' => 'View and manage vote plugin',

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

        'delay' => 'Delay between votes',
        'minutes' => 'minutes',

        'no-verification' => 'The votes on this website will not be verified.',
        'auto-verification' => 'The votes on this site will be automatically verified.',
        'key-verification' => 'The votes on this website will be verified when the input below is filled.',

        'verifications' => [
            'enable' => 'Enable votes verification',

            'server_id' => 'Server ID',
            'token' => 'Token',
            'api_key' => 'API key',
        ],

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

    'statistics' => [
        'title' => 'Statistics',
        'stats' => [
            'global' => 'Number of votes',
            'month' => 'Number of votes this month',
            'month-char' => 'Number of votes this month',
            'year-char' => 'Number of votes this year',
            'week' => 'Number of votes this week',
            'day' => 'Number of votes today',
        ],
    ],
];
