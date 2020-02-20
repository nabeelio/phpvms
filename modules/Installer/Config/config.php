<?php

return [
    'php' => [
        'version' => '7.2',
    ],

    'cache' => [
        // Default driver to use when no driver is present
        'default' => 'file',
        'drivers' => [
            'Zend OPcache' => 'opcache',
            'apc'          => 'apc',
        ],
    ],

    'extensions' => [
        'bcmath',
        'openssl',
        'pdo',
        'mbstring',
        'tokenizer',
        'json',
        'curl',
    ],

    // Make sure these are writable
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
