<?php

namespace App\Console\Cron;

use App\Console\Command;
use App\Events\CronHourly;

/**
 * This just calls the CronHourly event, so all of the
 * listeners, etc can just be called to run those tasks
 * @package App\Console\Cron
 */
class Hourly extends Command
{
    protected $signature = 'cron:hourly';
    protected $description = 'Run the hourly cron tasks';
    protected $schedule;

    /**
     *
     */
    public function handle(): void
    {
        $this->redirectLoggingToStdout('cron');
        event(new CronHourly());
    }
}
