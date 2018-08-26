<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is dispatched when the daily cron is run
 * It happens after all of the default nightly tasks
 */
class CronNightly
{
    use Dispatchable, SerializesModels;

    /**
     * CronNightly constructor.
     */
    public function __construct()
    {
    }
}
