<?php

return [
    'title' => 'Vote',

    'sections' => [
        'vote' => 'Voter',
        'top' => 'Classement',
        'rewards' => 'Récompenses',
    ],

    'fields' => [
        'chances' => 'Chances',
        'commands' => 'Commandes',
        'reward' => 'Récompense',
        'rewards' => 'Récompenses',
        'servers' => 'Serveurs',
        'site' => 'Site',
        'votes' => 'Votes',
    ],

    'errors' => [
        'user' => 'Cet utilisateur n\'existe pas.',
        'site' => 'Aucun site de vote n\'est disponible pour le moment.',
        'delay' => 'Vous avez déjà voté, vous pouvez voter à nouveau dans :time.',
        'auth' => 'Vous devez être connecté pour pouvoir voter.',
    ],

    'votes' => 'Vous avez voté :count fois ce mois-ci.',

    'server' => 'Choisissez le serveur sur lequel recevoir la récompense.',

    'success' => 'Votre vote a été pris en compte, vous recevrez bientôt la récompense ":reward" !',

    'notifications' => [
        'top' => 'Félicitions, vous avez reçu ":reward" pour avoir été le meilleur voteur #:position du mois!',
    ],
];
