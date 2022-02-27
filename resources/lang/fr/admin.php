<?php

return [
    'nav' => [
        'title' => 'Vote',
        'settings' => 'Paramètres',
        'sites' => 'Sites',
        'rewards' => 'Récompenses',
        'votes' => 'Votes',
    ],

    'permission' => 'Gérer le plugin vote',

    'settings' => [
        'title' => 'Paramètres de la page de vote',

        'count' => 'Nombre de joueurs dans le classement',
        'display-rewards' => 'Afficher les récompenses sur la page de vote',
        'ip_compatibility' => 'Activer la compatibilité IPv6',
        'ip_compatibility_info' => 'Cette option permet de corriger les votes qui ne se vérifient pas sur les sites de vote n\'acceptent pas l\'IPv6 alors que votre site oui, ou inversement.',
        'commands' => 'Commandes globales',
    ],

    'sites' => [
        'title' => 'Sites',
        'edit' => 'Modifier le site :site',
        'create' => 'Créer un site',

        'enable' => 'Activer le site',
        'delay' => 'Délai entre chaque vote',
        'minutes' => 'minutes',

        'list' => 'Sites sur lesquels les votes peuvent être vérifiés',
        'variable' => 'Vous pouvez utiliser <code>{player}</code> qui sera remplacé par le pseudo du joueur.',

        'verifications' => [
            'title' => 'Vérification',
            'enable' => 'Activer la vérification des votes',

            'disabled' => 'Les votes sur ce site ne seront pas vérifiés.',
            'auto' => 'Les votes sur ce site seront automatiquement vérifiés.',
            'input' => 'Les votes sur ce site seront vérifiés quand le champ ci-dessous est rempli.',

            'pingback' => 'URL de pingback: :url',
            'secret' => 'Clé secrete',
            'server_id' => 'ID du serveur',
            'token' => 'Token',
            'api_key' => 'Clé d\'API',
        ],
    ],

    'rewards' => [
        'title' => 'Récompenses',
        'edit' => 'Modifier la récompense :reward',
        'create' => 'Créer une récompense',

        'require_online' => 'Exécuter les commandes lorsque l\'utilisateur est en ligne sur le serveur (uniquement disponible avec AzLink)',
        'enable' => 'Activer la récompense',

        'commands' => 'Vous pouvez utiliser <code>{player}</code> pour utiliser le nom du joueur et <code>{reward}</code> pour utiliser le nom de la récompense. La commande ne doit pas contenir de <code>/</code> au début.',
    ],

    'votes' => [
        'title' => 'Votes',

        'empty' => 'Pas de votes ce mois-ci.',
        'votes' => 'Nombre de votes',
        'month' => 'Nombre de votes ce mois-ci',
        'week' => 'Nombre de votes cette semaine',
        'day' => 'Nombre de votes aujourd\'hui',
    ],

    'logs' => [
        'vote-sites' => [
            'created' => 'Création du site de vote #:id',
            'updated' => 'Mise à jour du site de vote #:id',
            'deleted' => 'Suppression du site de vote #:id',
        ],

        'vote-rewards' => [
            'created' => 'Création de la récompense de vote #:id',
            'updated' => 'Mise à jour de la récompense de vote #:id',
            'deleted' => 'Suppression de la récompense de vote #:id',
        ],
    ],
];
