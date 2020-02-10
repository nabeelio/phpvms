<?php

return [
    'default'           => 'http',
    'version_installed' => env('SELF_UPDATER_VERSION_INSTALLED', ''),

    'repository_types' => [
        'github' => [
            'type'                 => 'github',
            'repository_vendor'    => 'nabeelio',
            'repository_name'      => 'phpvms',
            'repository_url'       => 'https://github.com/nabeelio/phpvms',
            'download_path'        => env('SELF_UPDATER_DOWNLOAD_PATH', '/tmp'),
            'private_access_token' => '',
            'use_branch'           => 'master',
        ],
        'http' => [
            'type'                 => 'http',
            'repository_url'       => 'http://downloads.phpvms.net',
            'pkg_filename_format'  => 'phpvms-_VERSION_.zip',
            'download_path'        => env('SELF_UPDATER_DOWNLOAD_PATH', '/tmp'),
            'private_access_token' => env('SELF_UPDATER_HTTP_PRIVATE_ACCESS_TOKEN', ''),
        ],
    ],

    'exclude_folders' => [
        'node_modules',
        'bootstrap/cache',
        'bower',
        'storage/app',
        'storage/framework',
        'storage/logs',
        'storage/self-update',
    ],

    'log_events' => true,

    /*
    |---------------------------------------------------------------------------
    | Register custom artisan commands
    |---------------------------------------------------------------------------
    */

    'artisan_commands' => [
        'pre_update' => [
            //'command:signature' => [
            //    'class' => Command class
            //    'params' => []
            //]
        ],
        'post_update' => [

        ],
    ],

];
