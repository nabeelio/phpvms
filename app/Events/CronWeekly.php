<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is dispatched when the weekly cron is run
 * It happens after all of the default nightly tasks
 * @package App\Events
 */
class CronWeekly
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {

    }
}
