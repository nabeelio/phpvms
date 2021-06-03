<?php

return [
    /*
     *
     * Shared translations.
     *
     */
    'title'  => 'phpVMS Installer',
    'next'   => 'Nächster Schritt',
    'back'   => 'Letzter Schritt',
    'finish' => 'Installieren',
    'forms'  => [
        'errorTitle' => 'Folgender Fehler ist aufgetreten:',
    ],

    /*
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => 'Willkommen',
        'title'         => 'phpVMS Installer',
        'message'       => 'Installationsassistent.',
        'next'          => 'Vorraussetzungen prüfen',
    ],

    /*
     *
     * Requirements page translations.
     *
     */
    'requirements' => [
        'templateTitle' => 'Schritt 1 | Server Vorraussetzungen',
        'title'         => 'Server Vorraussetzungen',
        'next'          => 'Berechtigungen prüfen',
    ],

    /*
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'templateTitle' => 'Schritt 2 | Berechtigungen',
        'title'         => 'Berechtigungen',
        'next'          => 'Umgebungsvariablen konfigurieren',
    ],

    /*
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'menu' => [
            'templateTitle'  => 'Step 3 | Umgebungsvariablen',
            'title'          => 'Umgebungsvariablen',
            'desc'           => 'Bitte wähle aus wie Du die <code>.env</code>-Datei konfigurieren möchtest.',
            'wizard-button'  => 'Einrichtung mit Formularassistent',
            'classic-button' => 'Einrichtung mit Texteditor',
        ],
        'wizard' => [
            'templateTitle' => 'Step 3 | Umgebungsvariablen | Formularassistent',
            'title'         => '<code>.env</code> Assistent',
            'tabs'          => [
                'environment' => 'Environment',
                'database'    => 'Database',
                'application' => 'Application',
            ],
            'form' => [
                'name_required'                      => 'Ein Umgebungsname ist erforderlich.',
                'app_name_label'                     => 'App Name',
                'app_name_placeholder'               => 'App Name',
                'app_environment_label'              => 'App Environment',
                'app_environment_label_local'        => 'Lokal',
                'app_environment_label_developement' => 'Entwicklung',
                'app_environment_label_qa'           => 'Qa',
                'app_environment_label_production'   => 'Produktion',
                'app_environment_label_other'        => 'Other',
                'app_environment_placeholder_other'  => 'Geben Sie Ihre Umgebung ein...',
                'app_debug_label'                    => 'App Debug',
                'app_debug_label_true'               => 'True',
                'app_debug_label_false'              => 'False',
                'app_log_level_label'                => 'App Log Level',
                'app_log_level_label_debug'          => 'debug',
                'app_log_level_label_info'           => 'info',
                'app_log_level_label_notice'         => 'notice',
                'app_log_level_label_warning'        => 'warning',
                'app_log_level_label_error'          => 'error',
                'app_log_level_label_critical'       => 'critical',
                'app_log_level_label_alert'          => 'alert',
                'app_log_level_label_emergency'      => 'emergency',
                'app_url_label'                      => 'App URL',
                'app_url_placeholder'                => 'App URL',
                'db_connection_label'                => 'Datenbankverbindung',
                'db_connection_label_mysql'          => 'mysql',
                'db_connection_label_sqlite'         => 'sqlite',
                'db_connection_label_pgsql'          => 'pgsql',
                'db_connection_label_sqlsrv'         => 'sqlsrv',
                'db_host_label'                      => 'Database Host',
                'db_host_placeholder'                => 'Database Host',
                'db_port_label'                      => 'Database Port',
                'db_port_placeholder'                => 'Database Port',
                'db_name_label'                      => 'Database Name',
                'db_name_placeholder'                => 'Database Name',
                'db_username_label'                  => 'Database User Name',
                'db_username_placeholder'            => 'Database User Name',
                'db_password_label'                  => 'Database Passwort',
                'db_password_placeholder'            => 'Database Passwort',

                'app_tabs' => [
                    'more_info'                => 'More Info',
                    'broadcasting_title'       => 'Broadcasting, Caching, Session, &amp; Queue',
                    'broadcasting_label'       => 'Broadcast Driver',
                    'broadcasting_placeholder' => 'Broadcast Driver',
                    'cache_label'              => 'Cache Driver',
                    'cache_placeholder'        => 'Cache Driver',
                    'session_label'            => 'Session Driver',
                    'session_placeholder'      => 'Session Driver',
                    'queue_label'              => 'Queue Driver',
                    'queue_placeholder'        => 'Queue Driver',
                    'redis_label'              => 'Redis Driver',
                    'redis_host'               => 'Redis Host',
                    'redis_password'           => 'Redis Passwort',
                    'redis_port'               => 'Redis Port',

                    'mail_label'                  => 'Mail',
                    'mail_driver_label'           => 'Mail Driver',
                    'mail_driver_placeholder'     => 'Mail Driver',
                    'mail_host_label'             => 'Mail Host',
                    'mail_host_placeholder'       => 'Mail Host',
                    'mail_port_label'             => 'Mail Port',
                    'mail_port_placeholder'       => 'Mail Port',
                    'mail_username_label'         => 'Mail Username',
                    'mail_username_placeholder'   => 'Mail Username',
                    'mail_password_label'         => 'Mail Passwort',
                    'mail_password_placeholder'   => 'Mail Passwort',
                    'mail_encryption_label'       => 'Mail Encryption',
                    'mail_encryption_placeholder' => 'Mail Encryption',

                    'pusher_label'                  => 'Pusher',
                    'pusher_app_id_label'           => 'Pusher App Id',
                    'pusher_app_id_palceholder'     => 'Pusher App Id',
                    'pusher_app_key_label'          => 'Pusher App Key',
                    'pusher_app_key_palceholder'    => 'Pusher App Key',
                    'pusher_app_secret_label'       => 'Pusher App Secret',
                    'pusher_app_secret_palceholder' => 'Pusher App Secret',
                ],
                'buttons' => [
                    'setup_database'    => 'Datenbank installieren',
                    'setup_application' => 'Anwendung installieren',
                    'install'           => 'Installieren',
                ],
            ],
        ],
        'classic' => [
            'templateTitle' => 'Step 3 | Umgebungsvariablen | Text Editor',
            'title'         => 'Umgebungsvariableneditor',
            'save'          => '.env speichern',
            'back'          => 'Formularassistent benutzen',
            'install'       => 'Speichern und Installieren',
        ],
        'success' => 'Die .env Datei und die Einstellungen wurden gespeichert.',
        'errors'  => 'Konnte die .env Datei nicht erstellen, bitte erstelle sie Manuell.',
    ],

    'install' => 'Installieren',

    /*
     *
     * Installed Log translations.
     *
     */
    'installed' => [
        'success_log_message' => 'Laravel Installer wurde erfolgreich installiert am ',
    ],

    /*
     *
     * Final page translations.
     *
     */
    'final' => [
        'title'         => 'Installation abgeschlossen',
        'templateTitle' => 'Installation abgeschlossen',
        'finished'      => 'Anwendung wurde erfolgreich installiert.',
        'migration'     => 'Migration &amp; Seed Konsolenausgabe:',
        'console'       => 'Anwendungskonsole Ausgabe:',
        'log'           => 'Installationsprotokolleintrag:',
        'env'           => 'Finale .env Datei:',
        'exit'          => 'Klicke hier zum Verlassen',
    ],

    /*
     *
     * Update specific translations
     *
     */
    'updater' => [
        /*
         *
         * Shared translations.
         *
         */
        'title' => 'Laravel Updater',

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'welcome' => [
            'title'   => 'Willkommen im Updateassistent',
            'message' => 'Willkommen im Updateassistent',
        ],

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'overview' => [
            'title'           => 'Übersicht',
            'message'         => 'Es ist ein Update vorhanden|Es sind :number Updates vorhanden.',
            'install_updates' => 'Updates installieren',
        ],

        /*
         *
         * Final page translations.
         *
         */
        'final' => [
            'title'    => 'Abgeschlossen',
            'finished' => 'Anwendungsdatenbank wurde erfolgreich geupdatet.',
            'exit'     => 'Klicke hier zum verlassen',
        ],

        'log' => [
            'success_message' => 'Laravel Installer erfolgreicht UPDATED um ',
        ],
    ],
];
