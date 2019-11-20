<?php

namespace App\Events;

/**
 * This event is dispatched when the weekly cron is run
 * It happens after all of the default nightly tasks
 */
class CronWeekly extends BaseEvent
{
    public function __construct()
    {
    }
}
