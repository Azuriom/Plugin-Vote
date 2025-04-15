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
        'auth_required' => 'Require users to be logged in to vote',
        'commands' => 'Global commands',
    ],

    'sites' => [
        'title' => 'Sites',
        'edit' => 'Edit site :site',
        'create' => 'Create site',

        'enable' => 'Enable the site',
        'type' => 'Type of delay between votes',
        'interval' => 'Fixed interval between votes',
        'daily' => 'Specific time of day',
        'delay' => 'Delay between votes',
        'time' => 'Time to vote again',
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
        'single_server' => 'Let the user choose the server to receive the reward',

        'commands' => 'You can use <code>{player}</code> to use the player name, <code>{reward}</code> for the reward name and <code>{site}</code> for the vote website. For Steam games, you can also use <code>{steam_id}</code> and <code>{steam_id_32}</code>. The command must not start with <code>/</code>.',
        'monthly' => 'Ranking of users to give this reward to at the end of the month',
        'monthly_info' => 'Automatically give this reward, at the end of the month, to the users at the given positions in the best voters ranking.',
        'cron' => 'You must set up CRON tasks to use automatic rewards at the end of the month, see the <a href="https://azuriom.com/docs/installation" target="_blank" rel="noopener norefferer">documentation</a> for more information.',
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

        'settings' => 'Updated vote settings',
    ],
];
