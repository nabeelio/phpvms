<?php

return [
    'php' => [
        'version' => '7.2'
    ],

    'extensions' => [
        'calendar',
        'openssl',
        'pdo',
        'mbstring',
        'tokenizer',
        'json',
        'curl',
        'gmp',
    ],

    # Make sure these are writable
    'permissions' => [
        'bootstrap/cache',
        'public/uploads',
        'storage',
        'storage/app/public',
        'storage/app/public/avatars',
        'storage/app/public/uploads',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
    ],
];
