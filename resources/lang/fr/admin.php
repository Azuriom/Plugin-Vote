<?php

return [
    'nav' => [
        'title' => 'Vote',

        'settings' => 'Paramètres',
        'sites' => 'Sites',
        'rewards' => 'Récompenses',
    ],

    'settings' => [
        'title' => 'Paramètres de la page de vote',

        'count' => 'Nombre de joueurs dans le classement',
        'display-rewards' => 'Afficher les récompenses sur la page de vote',
    ],

    'sites' => [
        'title' => 'Sites',
        'title-edit' => 'Modifier le site :site',
        'title-create' => 'Créer un site',

        'enable' => 'Activer le site',

        'delay' => 'Délai entre chaque vote en minutes',

        'status' => [
            'created' => 'Le site a été ajouté.',
            'updated' => 'Le site a été mis à jour.',
            'deleted' => 'Le site a été supprimé.',
        ],
    ],

    'rewards' => [
        'title' => 'Récompenses',
        'title-edit' => 'Modifier la récompense :reward',
        'title-create' => 'Créer une récompense',

        'need-online' => 'L\'utilisateur doit être en ligne pour recevoir la récompense (uniquement disponible avec AzLink)',
        'enable' => 'Activer la récompense',

        'commands-info' => 'Vous pouvez utiliser <code>{player}</code> pour utiliser le nom du joueur et <code>{reward}</code> pour utiliser le nom de la récompense.',

        'status' => [
            'created' => 'La récompense a été créée.',
            'updated' => 'La récompense a été mise à jour.',
            'deleted' => 'La récompense a été supprimée.',
        ],
    ],
];
