<?php

return [

    'fetch' => PDO::FETCH_ASSOC,
    'default' => env('DB_CONNECTION', 'local'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', storage_path('db.sqlite')),
            'prefix' => '',
        ],
        'local' => [
            'driver' => 'sqlite',
            'database' => storage_path('local.sqlite'),
            'prefix' => '',
        ],
        'unittest' => [
            'driver' => 'sqlite',
            'database' => storage_path('unittest.sqlite'),
            'prefix' => '',
        ],
        'memory' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
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
            'database' => 0,
        ],
    ]
];
