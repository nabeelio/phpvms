<?php

use Modules\Installer\Services\Importer\Stages\Stage1;
use Modules\Installer\Services\Importer\Stages\Stage2;
use Modules\Installer\Services\Importer\Stages\Stage3;
use Modules\Installer\Services\Importer\Stages\Stage4;
use Modules\Installer\Services\Importer\Stages\Stage5;
use Modules\Installer\Services\Importer\Stages\Stage6;

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

    'importer' => [
        'batch_size' => 150,
        'stages'     => [
            'stage1' => Stage1::class,
            'stage2' => Stage2::class,
            'stage3' => Stage3::class,
            'stage4' => Stage4::class,
            'stage5' => Stage5::class,
            'stage6' => Stage6::class,
        ],
    ],
];
