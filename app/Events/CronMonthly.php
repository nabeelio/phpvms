<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event is dispatched when the monthly cron is run
 * It happens after all of the default nightly tasks
 * @package App\Events
 */
class CronMonthly
{
    use Dispatchable, SerializesModels;

    /**
     * CronMonthly constructor.
     */
    public function __construct()
    {

    }
}
