<?php
/**
 * This runs any of the cron tasks that are set to run according to the Laravel schedule
 */

namespace App\Console;

use App\Console\Cron\Hourly;
use App\Console\Cron\JobQueue;
use App\Console\Cron\Monthly;
use App\Console\Cron\Nightly;
use App\Console\Cron\Weekly;
use App\Contracts\Command;
use Illuminate\Console\Scheduling\Schedule;

/**
 *
 */
class Cron
{
    /** @var Schedule */
    private $scheduler;

    /**
     * @var string[] The cron tasks which get called/run
     */
    private $cronTasks = [
        Hourly::class,
        JobQueue::class,
        Monthly::class,
        Nightly::class,
        Weekly::class,
    ];

    /**
     * @var array Stores the instantiated cron tasks
     */
    private $cronRunners = [];

    /**
     * @param Schedule $scheduler
     */
    public function __construct(Schedule $scheduler)
    {
        $this->scheduler = $scheduler;
        foreach($this->cronTasks as $task) {
            /** @var Command $cronTask */
            $cronTask = app($task);
            $this->cronRunners[$cronTask->getSignature()] = $cronTask;
        }
    }

    /**
     * Try to figure out which commands are supposed to run right now
     *
     * @return void
     */
    public function run()
    {
        $events = $this->scheduler->dueEvents(app());

        /** @var \Illuminate\Console\Scheduling\Event $event */
        foreach ($events as $event) {
            foreach ($this->cronRunners as $signature => $task) {
                if (!str_contains($event->command, $signature)) {
                    continue;
                }

                break;
            }

            if (empty($task)) {
                continue;
            }

            $task->callEvent();
        }
    }
}
