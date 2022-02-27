<?php

return [
    'nav' => [
        'title' => 'Vote',
        'settings' => 'Settings',
        'sites' => 'Sites',
        'rewards' => 'Rewards',
        'votes' => 'Votes',
    ],

    'permission' => 'Manage vote plugin',

    'settings' => [
        'title' => 'Vote page settings',

        'count' => 'Top Players Count',
        'display-rewards' => 'Show rewards in vote page',
        'ip_compatibility' => 'Enable IPv4/IPv6 compatibility',
        'ip_compatibility_info' => 'This option allows you to correct votes that are not verified on voting sites that don\'t accept IPv6 while your site does, or vice versa.',
        'commands' => 'Global commands',
    ],

    'sites' => [
        'title' => 'Sites',
        'edit' => 'Edit site :site',
        'create' => 'Create site',

        'enable' => 'Enable the site',
        'delay' => 'Delay between votes',
        'minutes' => 'minutes',

        'list' => 'Sites on which votes can be verified',
        'variable' => 'You can use <code>{player}</code> to use the player name.',

        'verifications' => [
            'title' => 'Verification',
            'enable' => 'Enable votes verification',

            'disabled' => 'The votes on this website will not be verified.',
            'auto' => 'The votes on this site will be automatically verified.',
            'input' => 'The votes on this website will be verified when the input below is filled.',

            'pingback' => 'Pingback URL: :url',
            'secret' => 'Secret key',
            'server_id' => 'Server ID',
            'token' => 'Token',
            'api_key' => 'API key',
        ],
    ],

    'rewards' => [
        'title' => 'Rewards',
        'edit' => 'Edit reward :reward',
        'create' => 'Create reward',

        'require_online' => 'Execute commands when the user is online on the server (only available with AzLink)',
        'enable' => 'Enable the reward',

        'commands' => 'You can use <code>{player}</code> to use the player name and <code>{reward}</code> to use the reward name. The command must not start with <code>/</code>',
    ],

    'votes' => [
        'title' => 'Votes',

        'empty' => 'No votes this month.',
        'votes' => 'Votes count',
        'month' => 'Votes count this month',
        'week' => 'Votes count this week',
        'day' => 'Votes count today',
    ],

    'logs' => [
        'vote-sites' => [
            'created' => 'Created vote site #:id',
            'updated' => 'Updated vote site #:id',
            'deleted' => 'Deleted vote site #:id',
        ],

        'vote-rewards' => [
            'created' => 'Created vote reward #:id',
            'updated' => 'Updated vote reward #:id',
            'deleted' => 'Deleted vote reward #:id',
        ],
    ],
];
