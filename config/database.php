<?php

return [
    'fetch' => PDO::FETCH_ASSOC,
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            //'unix_socket' => env('DB_SOCKET', ''),
            'prefix' => env('DB_PREFIX', ''),
            'timezone' => '+00:00',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict' => false,
            'engine' => null,
            'options' => [
                PDO::ATTR_EMULATE_PREPARES => env('DB_EMULATE_PREPARES', false),
                #PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        ],
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => storage_path('db.sqlite'),
            'timezone' => '+00:00',
            'prefix' => '',
        ],
        'unittest' => [
            'driver' => 'sqlite',
            'database' => storage_path('unittest.sqlite'),
            'timezone' => '+00:00',
            'prefix' => '',
        ],
        'memory' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'timezone' => '+00:00',
            'prefix' => '',
        ],
    ],

    'migrations' => 'migrations',

    'redis' => [
        'cluster' => false,
        'default' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 1),
        ],
    ]
];
