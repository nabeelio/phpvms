<?php

return [
    'namespace' => 'Modules',
    'stubs'     => [
        'enabled' => true,
        'path'    => resource_path().'/stubs/modules',
        'files'   => [
            'routes'                 => 'Http/Routes/web.php',
            'routes-api'             => 'Http/Routes/api.php',
            'routes-admin'           => 'Http/Routes/admin.php',
            'event-service-provider' => 'Providers/EventServiceProvider.php',
            'views/index'            => 'Resources/views/index.blade.php',
            'views/index-admin'      => 'Resources/views/admin/index.blade.php',
            'views/frontend'         => 'Resources/views/layouts/frontend.blade.php',
            'views/admin'            => 'Resources/views/layouts/admin.blade.php',
            'listener-test'          => 'Listeners/TestEventListener.php',
            'controller-api'         => 'Http/Controllers/Api/ApiController.php',
            'controller-admin'       => 'Http/Controllers/Admin/AdminController.php',
            'scaffold/config'        => 'Config/config.php',
            'composer'               => 'composer.json',
        ],
        'replacements' => [
            'start'                  => ['LOWER_NAME', 'ROUTES_LOCATION'],
            'routes'                 => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'routes-api'             => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'json'                   => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'event-service-provider' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'listener-test'          => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'views/index'            => ['LOWER_NAME'],
            'views/index-admin'      => ['LOWER_NAME', 'STUDLY_NAME'],
            'views/frontend'         => ['STUDLY_NAME'],
            'views/admin'            => ['STUDLY_NAME'],
            'controller-admin'       => ['MODULE_NAMESPACE', 'STUDLY_NAME', 'CLASS_NAMESPACE', 'LOWER_NAME'],
            'controller-api'         => ['MODULE_NAMESPACE', 'STUDLY_NAME', 'CLASS_NAMESPACE', 'LOWER_NAME'],
            'scaffold/config'        => ['STUDLY_NAME'],
            'composer'               => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'MODULE_NAMESPACE',
            ],
        ],
        'gitkeep' => false,
    ],
    'paths' => [
        'modules'   => base_path('modules'),
        'assets'    => public_path('modules'),
        'migration' => base_path('database/migrations'),
        'generator' => [
            'config'           => ['path' => 'Config', 'generate' => true],
            'command'          => ['path' => 'Console', 'generate' => true],
            'migration'        => ['path' => 'Database/migrations', 'generate' => true],
            'seeds'            => ['path' => 'Database/seeds', 'generate' => true],
            'factory'          => ['path' => 'Database/factories', 'generate' => true],
            'model'            => ['path' => 'Models', 'generate' => true],
            'controller'       => ['path' => 'Http/Controllers', 'generate' => true],
            'controller-admin' => ['path' => 'Http/Controllers/Admin', 'generate' => true],
            'controller-api'   => ['path' => 'Http/Controllers/Api', 'generate' => true],
            'filter'           => ['path' => 'Http/Middleware', 'generate' => true],
            'request'          => ['path' => 'Http/Requests', 'generate' => true],
            'routes'           => ['path' => 'Http/Routes', 'generate' => true],
            'provider'         => ['path' => 'Providers', 'generate' => true],
            'assets'           => ['path' => 'Resources/assets', 'generate' => true],
            'lang'             => ['path' => 'Resources/lang', 'generate' => true],
            'views'            => ['path' => 'Resources/views', 'generate' => true],
            'test'             => ['path' => 'tests', 'generate' => true],
            'repository'       => ['path' => 'Repositories', 'generate' => false],
            'event'            => ['path' => 'Events', 'generate' => false],
            'listener'         => ['path' => 'Listeners', 'generate' => true],
            'policies'         => ['path' => 'Policies', 'generate' => false],
            'rules'            => ['path' => 'Rules', 'generate' => false],
            'jobs'             => ['path' => 'Jobs', 'generate' => false],
            'emails'           => ['path' => 'Resources/Emails', 'generate' => false],
            'notifications'    => ['path' => 'Notifications', 'generate' => false],
            'resource'         => ['path' => 'Models/Transformers', 'generate' => false],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    |
    | Here you define which folder will be scanned. By default will scan vendor
    | directory. This is useful if you host the package in packagist website.
    |
    */

    'scan' => [
        'enabled' => false,
        'paths'   => [
            base_path('vendor/*/*'),
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | Here is the config for composer.json file, generated by this package
    |
    */

    'composer' => [
        'vendor' => '',
        'author' => [
            'name'  => '',
            'email' => '',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here is the config for setting up caching feature.
    |
    */
    'cache' => [
        'enabled'  => true,
        'key'      => 'phpvms-modules',
        'lifetime' => 60,
    ],
    /*
    |--------------------------------------------------------------------------
    | Choose what laravel-modules will register as custom namespaces.
    | Setting one to false will require you to register that part
    | in your own Service Provider class.
    |--------------------------------------------------------------------------
    */
    'register' => [
        'translations' => true,
    ],
];
