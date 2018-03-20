<?php

namespace App\Console;

use App\Console\Cron\Monthly;
use App\Console\Cron\Nightly;
use App\Console\Cron\Weekly;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\AcarsReplay::class,
        Commands\CreateDatabase::class,
        Commands\DevCommands::class,
        Commands\YamlImport::class,
        Commands\ImportFromClassic::class,
        Commands\Install::class,
        Commands\NavdataImport::class,
        Commands\TestApi::class,
    ];

    /**
     * Define the application's command schedule.
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(Nightly::class)->dailyAt('01:00');
        $schedule->command(Weekly::class)->weeklyOn(0);
        $schedule->command(Monthly::class)->monthlyOn(1);
    }

    /**
     * Register the Closure based commands for the application.
     * @return void
     */
    protected function commands(): void
    {
        require app_path('Routes/console.php');
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/Cron');
    }
}
