<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is dispatched when the hourly cron is run
 * @package App\Events
 */
class CronHourly
{
    use Dispatchable, SerializesModels;

    /**
     * CronNightly constructor.
     */
    public function __construct()
    {

    }
}