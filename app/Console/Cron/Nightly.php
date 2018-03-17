<?php

namespace App\Console\Cron;

use App\Console\BaseCommand;
use App\Events\CronNightly;

/**
 * This just calls the CronNightly event, so all of the
 * listeners, etc can just be called to run those tasks
 * @package App\Console\Cron
 */
class Nightly extends BaseCommand
{
    protected $signature = 'cron:nightly';
    protected $description = 'Run the nightly cron tasks';
    protected $schedule;

    public function handle(): void
    {
        $this->redirectLoggingToStdout('cron');
        event(new CronNightly());
    }
}
