<?php

use App\Notifications\Notifiables\Backups;
use Spatie\Backup\Notifications\Notifications\BackupHasFailed;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessful;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailed;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessful;
use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFound;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFound;
use Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes;

return [
    'backup' => [
        'name'    => config('app.name', 'phpvms-backup'),
        'enabled' => false,
        'source'  => [
            'files' => [
                'include' => [
                    base_path(),
                ],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],
                'follow_links' => false,
            ],

            /*
             * The names of the connections to the databases that should be backed up
             * MySQL, PostgreSQL, SQLite and Mongo databases are supported.
             *
             * The content of the database dump may be customized for each connection
             * by adding a 'dump' key to the connection settings in config/database.php.
             * E.g.
             * 'mysql' => [
             *       ...
             *      'dump' => [
             *           'excludeTables' => [
             *                'table_to_exclude_from_backup',
             *                'another_table_to_exclude'
             *            ]
             *       ],
             * ],
             *
             * If you are using only InnoDB tables on a MySQL server, you can
             * also supply the useSingleTransaction option to avoid table locking.
             *
             * E.g.
             * 'mysql' => [
             *       ...
             *      'dump' => [
             *           'useSingleTransaction' => true,
             *       ],
             * ],
             *
             * For a complete list of available customization options, see https://github.com/spatie/db-dumper
             */
            'databases' => [
                'mysql',
            ],
        ],

        /*
         * The database dump can be compressed to decrease diskspace usage.
         *
         * Out of the box Laravel-backup supplies
         * Spatie\DbDumper\Compressors\GzipCompressor::class.
         *
         * You can also create custom compressor. More info on that here:
         * https://github.com/spatie/db-dumper#using-compression
         *
         * If you do not want any compressor at all, set it to null.
         */
        'database_dump_compressor' => Spatie\DbDumper\Compressors\GzipCompressor::class,

        'destination' => [

            /*
             * The filename prefix used for the backup zip file.
             */
            'filename_prefix' => '',

            /*
             * The disk names on which the backups will be stored.
             */
            'disks' => [
                'local',
            ],
        ],

        /*
         * The directory where the temporary files will be stored.
         */
        'temporary_directory' => storage_path('app/backup-temp'),

        /**
         * Encryption disabled by default
         */
        'encryption' => false,
    ],

    /*
     * You can get notified when specific events occur. Out of the box you can use 'mail' and 'slack'.
     * For Slack you need to install guzzlehttp/guzzle and laravel/slack-notification-channel.
     *
     * You can also use your own notification classes, just make sure the class is named after one of
     * the `Spatie\Backup\Events` classes.
     */
    'notifications' => [
        'notifications' => [
            BackupHasFailed::class         => ['mail'],
            UnhealthyBackupWasFound::class => ['mail'],
            CleanupHasFailed::class        => ['mail'],
            BackupWasSuccessful::class     => ['mail'],
            HealthyBackupWasFound::class   => ['mail'],
            CleanupWasSuccessful::class    => ['mail'],
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent. The default
         * notifiable will use the variables specified in this config file.
         */
        'notifiable' => Backups::class,
        'slack'      => [
            'webhook_url' => '',
            /*
             * If this is set to null the default channel of the webhook will be used.
             */
            'channel'  => null,
            'username' => null,
            'icon'     => null,
        ],
    ],

    /*
     * Here you can specify which backups should be monitored.
     * If a backup does not meet the specified requirements the
     * UnHealthyBackupWasFound event will be fired.
     */
    'monitor_backups' => [
        [
            'name'          => config('app.name'),
            'disks'         => ['local'],
            'health_checks' => [
                MaximumAgeInDays::class          => 1,
                MaximumStorageInMegabytes::class => 5000,
            ],
        ],

        /*
        [
            'name' => 'name of the second app',
            'disks' => ['local', 's3'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
        */
    ],

    'cleanup' => [
        /*
         * The strategy that will be used to cleanup old backups. The default strategy
         * will keep all backups for a certain amount of days. After that period only
         * a daily backup will be kept. After that period only weekly backups will
         * be kept and so on.
         *
         * No matter how you configure it the default strategy will never
         * delete the newest backup.
         */
        'strategy' => DefaultStrategy::class,

        'default_strategy' => [

            /*
             * The number of days for which backups must be kept.
             */
            'keep_all_backups_for_days' => 3,

            /*
             * The number of days for which daily backups must be kept.
             */
            'keep_daily_backups_for_days' => 2,

            /*
             * The number of weeks for which one weekly backup must be kept.
             */
            'keep_weekly_backups_for_weeks' => 2,

            /*
             * The number of months for which one monthly backup must be kept.
             */
            'keep_monthly_backups_for_months' => 1,

            /*
             * The number of years for which one yearly backup must be kept.
             */
            'keep_yearly_backups_for_years' => 1,

            /*
             * After cleaning up the backups remove the oldest backup until
             * this amount of megabytes has been reached.
             */
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],
];
