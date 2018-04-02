<?php

return [
    'php' => [
        'version' => '7.1.3'
    ],

    'extensions' => [
        'openssl',
        'pdo',
        'mbstring',
        'tokenizer',
        'json',
        'curl',
    ],

    # Make sure these are writable
    'permissions' => [
        'bootstrap/cache',
        'public/uploads',
        'storage',
        'storage/app/public',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
    ],
];
