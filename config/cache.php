<?php

return [

    'default' => env('CACHE_DRIVER', 'file'),
    'prefix' => env('CACHE_PREFIX', ''),

    'keys' => [
        'RANKS_PILOT_LIST' => [
            'key' => 'ranks::pilot_list',
            'time' => 1440,
        ]
    ],

    'stores' => [

        'apc' => ['driver' => 'apc'],
        'array' => ['driver' => 'array'],
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl'       => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options'    => [
                // Memcached::OPT_CONNECT_TIMEOUT  => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

    ],
];
