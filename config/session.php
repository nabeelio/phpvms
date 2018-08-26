<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default session "driver" that will be used on
    | requests. By default, we will use the lightweight native driver but
    | you may specify any of the other wonderful drivers provided here.
    |
    | Supported: "file", "cookie", "database", "apc",
    |            "memcached", "redis", "array"
    |
    */

    'driver'          => env('SESSION_DRIVER', 'file'),
    'lifetime'        => 120,
    'expire_on_close' => false,
    'encrypt'         => false,
    'files'           => storage_path('framework/sessions'),
    'connection'      => null,
    'table'           => 'sessions',
    'store'           => null,
    'lottery'         => [1, 100],
    'cookie'          => 'phpvms_session',
    'path'            => '/',
    'domain'          => env('SESSION_DOMAIN', null),
    'secure'          => false,
    'http_only'       => true,
];
