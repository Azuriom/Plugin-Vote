<?php

return [
    'nav' => [
        'title' => 'Abstimmen',
        'settings' => 'Einstellungen',
        'sites' => 'Seiten',
        'rewards' => 'Belohnungen',
        'votes' => 'Abstimmungen',
    ],

    'permission' => 'Abtimmungs-Plugin verwalten',

    'settings' => [
        'title' => 'Einstellungen der Abstimmungsseite',

        'count' => 'Anzahl der Top-Spieler',
        'display-rewards' => 'Belohnungen auf der Abstimmungsseite anzeigen',
        'ip_compatibility' => 'IPv4/IPv6-Kompatibilität aktivieren',
        'ip_compatibility_info' => 'Mit dieser Option kannst du Abstimmungen korrigieren, die auf Abstimmungsseiten, die IPv6 nicht akzeptieren, nicht verifiziert wurden, während deine Seite IPv6 akzeptiert, oder andersherum.',
        'commands' => 'Globale Befehle',
    ],

    'sites' => [
        'title' => 'Seiten',
        'edit' => 'Seite :site bearbeiten',
        'create' => 'Seite erstellen',

        'enable' => 'Aktiviere die Seite',
        'delay' => 'Verzögerung zwischen Abstimmungen',
        'minutes' => 'Minuten',

        'list' => 'Seiten, auf denen Stimmen überprüft werden können',
        'variable' => 'Du kannst <code>{player}</code> verwenden, um den Spielernamen zu verwenden.',

        'verifications' => [
            'title' => 'Verifizierung',
            'enable' => 'Stimmenüberprüfung aktivieren',

            'disabled' => 'Die Stimmen auf dieser Website werden nicht überprüft.',
            'auto' => 'Die Stimmen auf dieser Seite werden automatisch überprüft.',
            'input' => 'Die Stimmen auf dieser Website werden verifiziert, wenn die Eingabe unten ausgefüllt ist.',

            'pingback' => 'Pingback URL: :url',
            'secret' => 'Geheimschlüssel',
            'server_id' => 'Server ID',
            'token' => 'Token',
            'api_key' => 'API-Schlüssel',
        ],
    ],

    'rewards' => [
        'title' => 'Belohnungen',
        'edit' => 'Belohnung :reward bearbeiten',
        'create' => 'Belohnung erstellen',

        'require_online' => 'Befehle ausführen, wenn der Benutzer auf dem Server online ist (nur mit AzLink verfügbar)',
        'enable' => 'Aktiviere die Belohnung',

        'commands' => 'Du kannst <code>{player}</code> verwenden, um den Spielernamen zu verwenden, <code>{reward}</code> für den Belohnungsnamen und <code>{site}</code> für die Abstimmungswebsite. Für Steam-Spiele kannst du auch <code>{steam_id}</code> und <code>{steam_id_32}</code> verwenden. Der Befehl darf nicht mit <code>/</code> beginnen.',
        'monthly' => 'Rangliste der Nutzer, die diese Belohnung am Ende des Monats erhalten sollen',
        'monthly_info' => 'Verteile diese Belohnung am Ende des Monats automatisch an die Nutzer/innen, die in der Rangliste der besten Wähler/innen an der entsprechenden Stelle stehen.',
        'cron' => 'Du musst CRON-Aufgaben einrichten, um automatische Belohnungen am Ende des Monats zu verwenden.',
    ],

    'votes' => [
        'title' => 'Stimmen',

        'empty' => 'Keine Abstimmungen diesen Monat.',
        'votes' => 'Stimmen Anzahl',
        'month' => 'Stimmen Anzahl diesen Monat',
        'week' => 'Stimmen Anzahl diese Woche',
        'day' => 'Stimmen Anzahl heute',
    ],

    'logs' => [
        'vote-sites' => [
            'created' => 'Abstimmungsseite #:id erstellt',
            'updated' => 'Abstimmungsseite #:id aktualisiert',
            'deleted' => 'Abstimmungsseite #:id gelöscht',
        ],

        'vote-rewards' => [
            'created' => 'Abstimmungsbelohnung #:id erstellt',
            'updated' => 'Abstimmungsbelohnung #:id aktualisiert',
            'deleted' => 'Abstimmungsbelohnung #:id gelöscht',
        ],
    ],
];