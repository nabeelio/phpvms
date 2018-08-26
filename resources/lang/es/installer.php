<?php

return [
    /*
     *
     * Shared translations.
     *
     */
    'title'  => 'Instalador de phpVMS',
    'next'   => 'Siguiente',
    'back'   => 'Anterior',
    'finish' => 'Instalar',
    'forms'  => [
        'errorTitle' => 'Ocurrieron los siguientes errores:',
    ],

    /*
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => 'Bienvenido',
        'title'         => 'Instalador de phpVMS',
        'message'       => 'Instalación fácil y asistente de configuración.',
        'next'          => 'Comprobar requisitios',
    ],

    /*
     *
     * Requirements page translations.
     *
     */
    'requirements' => [
        'templateTitle' => 'Paso 1 | Requisitos del Servidor',
        'title'         => 'Requisitos del servidor',
        'next'          => 'Comprobar permisos',
    ],

    /*
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'templateTitle' => 'Paso 2 | Permisos',
        'title'         => 'Permisos',
        'next'          => 'Configurar entorno',
    ],

    /*
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'menu' => [
            'templateTitle'  => 'Paso 3 | Configuración de entorno',
            'title'          => 'Configuraciones de entorno',
            'desc'           => 'Seleccione cómo desea configurar las aplicaciones <code>.env</code> archivo.',
            'wizard-button'  => 'Desde el asistente',
            'classic-button' => 'Editor de texto clásico',
        ],
        'wizard' => [
            'templateTitle' => 'Paso 3 | Configuraciones de entorno | Asistente guíado',
            'title'         => 'Asistente <code>.env</code> guíado',
            'tabs'          => [
                'environment' => 'Entorno',
                'database'    => 'Base de datos',
                'application' => 'Aplicación',
            ],
            'form' => [
                'name_required'                      => 'Un nombre de entorno es requerido.',
                'app_name_label'                     => 'Nombre de la aplicación',
                'app_name_placeholder'               => 'Nombre de la aplicación',
                'app_environment_label'              => 'Entorno de aplicación',
                'app_environment_label_local'        => 'Local',
                'app_environment_label_developement' => 'Desarrollo',
                'app_environment_label_qa'           => 'QA',
                'app_environment_label_production'   => 'Producción',
                'app_environment_label_other'        => 'Otra',
                'app_environment_placeholder_other'  => 'Introduce tu entorno...',
                'app_debug_label'                    => 'Debug de aplicación',
                'app_debug_label_true'               => 'Verdadero',
                'app_debug_label_false'              => 'Falso',
                'app_log_level_label'                => 'Nivel de LOG de la aplicación',
                'app_log_level_label_debug'          => 'debug',
                'app_log_level_label_info'           => 'info',
                'app_log_level_label_notice'         => 'aviso',
                'app_log_level_label_warning'        => 'advertencia',
                'app_log_level_label_error'          => 'error',
                'app_log_level_label_critical'       => 'critico',
                'app_log_level_label_alert'          => 'alerta',
                'app_log_level_label_emergency'      => 'emergencía',
                'app_url_label'                      => 'URL de la App',
                'app_url_placeholder'                => 'URL App ',
                'db_connection_label'                => 'Conexión base de datos',
                'db_connection_label_mysql'          => 'mysql',
                'db_connection_label_sqlite'         => 'sqlite',
                'db_connection_label_pgsql'          => 'pgsql',
                'db_connection_label_sqlsrv'         => 'sqlsrv',
                'db_host_label'                      => 'Database: Host',
                'db_host_placeholder'                => 'Database: Host',
                'db_port_label'                      => 'Database: Puerto',
                'db_port_placeholder'                => 'Database: Puerto',
                'db_name_label'                      => 'Database: Nombre',
                'db_name_placeholder'                => 'Database: Nombre',
                'db_username_label'                  => 'Database: Nombre usuario',
                'db_username_placeholder'            => 'Database: Nombre usuario',
                'db_password_label'                  => 'Database: Contraseña',
                'db_password_placeholder'            => 'Database: Contraseña',

                'app_tabs' => [
                    'more_info'                => 'Más info',
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
                    'setup_database'    => 'Configurar base de datos',
                    'setup_application' => 'Configurar aplicación',
                    'install'           => 'Instalar',
                ],
            ],
        ],
        'classic' => [
            'templateTitle' => 'Paso 3 | Configuración de entorno | Editor clásico',
            'title'         => 'Editor de entorno cásico',
            'save'          => 'Guardar .env',
            'back'          => 'Usar el asistente de formulario',
            'install'       => 'Guardar e instalar',
        ],
        'success' => 'Tu archivo de configuración .env ha sido guardado.',
        'errors'  => 'No se ha guardado el archivo .env , Crealo manualmente.',
    ],

    'install' => 'Instalar',

    /*
     *
     * Installed Log translations.
     *
     */
    'installed' => [
        'success_log_message' => 'Inslatador Laravel exitosamente creado en ',
    ],

    /*
     *
     * Final page translations.
     *
     */
    'final' => [
        'title'         => 'Instalación finalizada',
        'templateTitle' => 'Instalación finalizada',
        'finished'      => 'La aplicación ha sido instalada exitosamente.',
        'migration'     => 'Migración &amp; salida de la consola:',
        'console'       => 'Salida de la consola de la aplicación:',
        'log'           => 'Instalación Log de entrada:',
        'env'           => 'Final .env archivo:',
        'exit'          => 'Clic aquí para salir',
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
        'title' => 'Actualizador Laravel',

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'welcome' => [
            'title'   => 'Bienvenido al actualizador',
            'message' => 'Bienvenido al asistente de actualización.',
        ],

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'overview' => [
            'title'           => 'Resumen',
            'message'         => 'Hay 1 actualización.|Hay :number actualizaciones.',
            'install_updates' => 'Instalar actualizaciones',
        ],

        /*
         *
         * Final page translations.
         *
         */
        'final' => [
            'title'    => 'Finalizado',
            'finished' => 'Aplicación/es actualizada/s con éxito.',
            'exit'     => 'Clic aquí para salir',
        ],

        'log' => [
            'success_message' => 'Inslatador Laravel exitosamente actualizado en ',
        ],
    ],
];
