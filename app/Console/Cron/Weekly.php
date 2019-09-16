<?php

namespace App\Console\Cron;

use App\Contracts\Command;
use App\Events\CronWeekly;

/**
 * This just calls the CronWeekly event, so all of the
 * listeners, etc can just be called to run those tasks.
 *
 * The actual cron tasks are in app/Cron
 */
class Weekly extends Command
{
    protected $signature = 'cron:weekly';
    protected $description = 'Run the weekly cron tasks';
    protected $schedule;

    public function handle(): void
    {
        $this->redirectLoggingToFile('cron');
        event(new CronWeekly());
    }
}
