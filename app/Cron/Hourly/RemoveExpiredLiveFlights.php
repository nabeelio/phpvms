<?php

namespace App\Cron\Hourly;

use App\Events\CronHourly;
use App\Interfaces\Listener;
use App\Models\Pirep;
use Carbon\Carbon;

/**
 * Remove expired live flights
 */
class RemoveExpiredLiveFlights extends Listener
{
    /**
     * Remove expired live flights
     *
     * @param CronHourly $event
     *
     * @throws \Exception
     */
    public function handle(CronHourly $event): void
    {
        if (setting('acars.live_time') === 0) {
            return;
        }

        $date = Carbon::now()->subHours(setting('acars.live_time'));
        Pirep::whereDate('created_at', '<', $date)
            ->where('state', PirepState::IN_PROGRESS)
            ->delete();
    }
}
