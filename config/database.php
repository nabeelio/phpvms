<?php

return [
    'fetch' => PDO::FETCH_ASSOC,
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => '',
            'username' => '',
            'password' => '',
            //'unix_socket' => env('DB_SOCKET', ''),
            'prefix' => '',
            'timezone' => '+00:00',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict' => false,
            'engine' => null,
            'options' => [
                PDO::ATTR_EMULATE_PREPARES => false,
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
            'host' => 'localhost',
            'password' => null,
            'port' => 6379,
            'database' => 1,
        ],
    ]
];
