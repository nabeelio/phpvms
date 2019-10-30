<?php

namespace App\Console;

use App\Console\Cron\Hourly;
use App\Console\Cron\Monthly;
use App\Console\Cron\Nightly;
use App\Console\Cron\Weekly;
use App\Services\CronService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    private $cronSvc;

    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);
        $this->cronSvc = app(CronService::class);
    }

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

        // Update the last time the cron was run
        $this->cronSvc->updateLastRunTime();
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
