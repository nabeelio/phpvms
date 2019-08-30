<?php

namespace App\Console;

use App\Console\Cron\Hourly;
use App\Console\Cron\Monthly;
use App\Console\Cron\Nightly;
use App\Console\Cron\Weekly;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(Nightly::class)->dailyAt('01:00');
        $schedule->command(Weekly::class)->weeklyOn(0);
        $schedule->command(Monthly::class)->monthlyOn(1);
        $schedule->command(Hourly::class)->hourly();

        // When spatie-backups runs
        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run')->daily()->at('02:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        require app_path('Http/Routes/console.php');
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/Cron');
    }
}
