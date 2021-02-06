<?php

return [
    /*
     *
     * Shared translations.
     *
     */
    'title'  => 'phpVMS Installer',
    'next'   => 'Nächster Schritt',
    'back'   => 'Zurück',
    'finish' => 'Installieren',
    'forms'  => [
        'errorTitle' => 'Folgende Fehler sind aufgetreten:',
    ],

    /*
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => 'Willkommen',
        'title'         => 'phpVMS Installer',
        'message'       => 'Einfacher Installations- und Einrichtungsassistent.',
        'next'          => 'Überprüfen Sie die Anforderungen',
    ],

    /*
     *
     * Requirements page translations.
     *
     */
    'requirements' => [
        'templateTitle' => 'Schritt 1 | Server Anforderungen',
        'title'         => 'Server Anforderungen',
        'next'          => 'Überprüfen Sie die Berechtigungen',
    ],

    /*
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'templateTitle' => 'Schritt 2 | Berechtigungen',
        'title'         => 'Berechtigungen',
        'next'          => 'Umgebung konfigurieren',
    ],

    /*
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'menu' => [
            'templateTitle'  => 'Schritt 3 | Umgebungseinstellungen',
            'title'          => 'Umgebungseinstellungen',
            'desc'           => 'Wählen Sie bitte aus wie Sie diese App konfigurieren möchten <code>.env</code> .', 
            'wizard-button'  => 'Formularassistent einrichten',
            'classic-button' => 'Klassischer Text Editor',
        ],
        'wizard' => [
            'templateTitle' => 'Schritt 3 | Umgebungseinstellungen | Geführter Assistent',
            'title'         => 'Geführter <code>.env</code> Assistent',
            'tabs'          => [
                'environment' => 'Umgebung',
                'database'    => 'Datenbank',
                'application' => 'Anwendung',
            ],
            'form' => [
                'name_required'                      => 'Ein Umgebungsname ist erforderlich.',
                'app_name_label'                     => 'App Name',
                'app_name_placeholder'               => 'App Name',
                'app_environment_label'              => 'App Umgebung',
                'app_environment_label_local'        => 'Lokal',
                'app_environment_label_developement' => 'Entwicklung',
                'app_environment_label_qa'           => 'Qa',
                'app_environment_label_production'   => 'Produktion',
                'app_environment_label_other'        => 'Andere',
                'app_environment_placeholder_other'  => 'Betreten Sie Ihre Umgebung...',
                'app_debug_label'                    => 'App Debug',
                'app_debug_label_true'               => 'Richtig',
                'app_debug_label_false'              => 'Falsch',
                'app_log_level_label'                => 'App Log Level',
                'app_log_level_label_debug'          => 'debug',
                'app_log_level_label_info'           => 'info',
                'app_log_level_label_notice'         => 'notice',
                'app_log_level_label_warning'        => 'warnung',
                'app_log_level_label_error'          => 'error',
                'app_log_level_label_critical'       => 'kritisch',
                'app_log_level_label_alert'          => 'alarm',
                'app_log_level_label_emergency'      => 'notfall',
                'app_url_label'                      => 'App Url',
                'app_url_placeholder'                => 'App Url',
                'db_connection_label'                => 'Datenbank Verbindung',
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
                'db_password_label'                  => 'Database Password',
                'db_password_placeholder'            => 'Database Password',

                'app_tabs' => [
                    'more_info'                => 'Mehr Informationen',
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
                    'redis_password'           => 'Redis Password',
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
                    'mail_password_label'         => 'Mail Password',
                    'mail_password_placeholder'   => 'Mail Password',
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
                    'setup_database'    => 'Setup Database',
                    'setup_application' => 'Setup Application',
                    'install'           => 'Install',
                ],
            ],
        ],
        'classic' => [
            'templateTitle' => 'Schritt 3 | Umgebungseinstellungen | Klassischer Editor',
            'title'         => 'Klassicher Umgebungs Editor',
            'save'          => 'Speichere .env',
            'back'          => 'Verwenden Sie den Formularassistenten',
            'install'       => 'Speichern und installieren',
        ],
        'success' => 'Ihre .env Datei Einstellungen wurden gespeichert.', 
        'errors'  => '.env Datei konnte nicht gespeichert werden, bitte manuell erstellen.',
    ],

    'install' => 'Installieren',

    /*
     *
     * Installed Log translations.
     *
     */
    'installed' => [
        'success_log_message' => 'Laravel Installer successfully INSTALLED on ',
    ],

    /*
     *
     * Final page translations.
     *
     */
    'final' => [
        'title'         => 'Installation beendet',
        'templateTitle' => 'Installation beendet',
        'finished'      => 'Anwendung wurde erfolgreich gespeichert.',
        'migration'     => 'Migration &amp; Seed Console Output:',
        'console'       => 'Application Console Output:',
        'log'           => 'Installation Log Entry:',
        'env'           => 'Final .env File:',
        'exit'          => 'Click here to exit',
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
            'title'   => 'Willkommen beim Updater',
            'message' => 'Willkommen beim Update-Assistenten.',
        ],

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'overview' => [
            'title'           => 'Überblick',
            'message'         => 'Es gibt 1 Update.|Es gibt :number updates.',
            'install_updates' => 'Install Updates',
        ],

        /*
         *
         * Final page translations.
         *
         */
        'final' => [
            'title'    => 'Beendet',
            'finished' => 'Die Datenbank der Anwendung wurde erfolgreich aktualisiert.',
            'exit'     => 'Hier klicken zum beenden',
        ],

        'log' => [
            'success_message' => 'Laravel Installer successfully UPDATED on ',
        ],
    ],
];
