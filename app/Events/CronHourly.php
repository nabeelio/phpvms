<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is dispatched when the hourly cron is run
 */
class CronHourly
{
    use Dispatchable, SerializesModels;

    /**
     * CronHourly constructor.
     */
    public function __construct()
    {
    }
}
