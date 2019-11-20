<?php

namespace App\Events;

/**
 * This event is dispatched when the daily cron is run
 * It happens after all of the default nightly tasks
 */
class CronNightly extends BaseEvent
{

}
