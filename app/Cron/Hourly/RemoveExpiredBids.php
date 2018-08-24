<?php

namespace App\Cron\Hourly;

use App\Events\CronHourly;
use App\Interfaces\Listener;
use App\Models\Bid;
use Carbon\Carbon;

/**
 * Remove expired bids
 * @package App\Listeners\Cron\Hourly
 */
class RemoveExpiredBids extends Listener
{
    /**
     * Remove expired bids
     * @param CronHourly $event
     * @throws \Exception
     */
    public function handle(CronHourly $event): void
    {
        if(setting('bids.expire_time') === 0) {
            return;
        }

        $date = Carbon::now()->subHours(setting('bids.expire_time'));
        Bid::whereDate('created_at', '<', $date)->delete();
    }
}
