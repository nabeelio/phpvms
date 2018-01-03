<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AcarsReplay::class,
        Commands\CreateDatabase::class,
        Commands\DevCommands::class,
        Commands\ImportCommand::class,
        Commands\Importer::class,
        Commands\Install::class,
        Commands\NavdataCommand::class,
        Commands\TestApi::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require app_path('Routes/console.php');
        $this->load(__DIR__ . '/Commands');
    }
}
