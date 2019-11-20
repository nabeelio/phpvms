<?php

namespace App\Events;

/**
 * This event is dispatched when the monthly cron is run
 * It happens after all of the default nightly tasks
 */
class CronMonthly extends BaseEvent
{
}
