<?php

use Illuminate\Validation\Rule;

return [
    'php' => [
        'version' => '7.0.0'
    ],

    # TODO: Remove eventually
    'env_postfix' => '.generated',

    'extensions' => [
        'openssl',
        'pdo',
        'mbstring',
        'tokenizer',
        'JSON',
        'cURL',
    ],

    'permissions' => [
        'storage/framework/' => 'writeable',
        'storage/logs/' => 'writeable',
        'bootstrap/cache/' => 'writable'
    ],
];
