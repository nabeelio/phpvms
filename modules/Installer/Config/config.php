<?php

return [
    'php' => [
        'version' => '7.0.0'
    ],

    'extensions' => [
        'openssl',
        'pdo',
        'mbstring',
        'tokenizer',
        'JSON',
        'cURL',
    ],

    # Make sure these are writable
    'permissions' => [
        'bootstrap/cache',
        'storage',
        'storage/app/public',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
    ],
];
