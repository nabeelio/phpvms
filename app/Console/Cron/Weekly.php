<?php

namespace App\Console\Cron;

use App\Console\Command;
use App\Events\CronMonthly;

/**
 * This just calls the CronWeekly event, so all of the
 * listeners, etc can just be called to run those tasks
 * @package App\Console\Cron
 */
class Weekly extends Command
{
    protected $signature = 'cron:monthly';
    protected $description = 'Run the monthly cron tasks';
    protected $schedule;

    /**
     *
     */
    public function handle(): void
    {
        $this->redirectLoggingToStdout('cron');
        event(new CronMonthly());
    }
}
