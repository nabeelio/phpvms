<?php

return [
    'default' => 'local',

    // This is the filesystem the uploaded files should go to
    'public_files' => 'public',
    'cloud'        => 's3',
    'disks'        => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],

        'seeds' => [
            'driver' => 'local',
            'root'   => database_path('seeds'),
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => public_path('uploads'),
            'url'        => '/uploads',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ],
    ],
];
