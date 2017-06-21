<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */

    'fetch' => PDO::FETCH_OBJ,
    'default' => env('DB_CONNECTION', 'local'),

    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
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

        'local' => [
            'driver' => 'sqlite',
            'database' => database_path('testing.sqlite'),
            'prefix' => '',
        ],

        'unittest' => [
            'driver' => 'sqlite',
            'database' => database_path('unittest.sqlite'),
            'prefix' => '',
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
