<?php

namespace App\Console\Cron;

use App\Contracts\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * This just calls the CronHourly event, so all of the
 * listeners, etc can just be called to run those tasks
 */
class JobQueue extends Command
{
    protected $signature = 'cron:queue';
    protected $description = 'Run the cron queue tasks';
    protected $schedule;

    public function handle(): void
    {
        $this->redirectLoggingToFile('cron');
        Artisan::call('queue:cron');

        $this->info(Artisan::output());
    }
}
