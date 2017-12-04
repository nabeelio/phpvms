<?php

return [

    'fetch' => PDO::FETCH_ASSOC,
    'default' => env('DB_CONNECTION', 'local'),

    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'phpvms'),
            'username' => env('DB_USERNAME', 'phpvms'),
            'password' => env('DB_PASSWORD', 'phpvms'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => storage_path('db.sqlite'),
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

        'travis' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'testing' => [
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

    ],

];
