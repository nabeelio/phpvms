<?php

namespace App\Cron\Hourly;

use App\Contracts\Listener;
use App\Events\CronHourly;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use Carbon\Carbon;

/**
 * Remove expired live flights
 */
class RemoveExpiredLiveFlights extends Listener
{
    /**
     * Remove expired live flights that haven't had an update in the live time
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
        Pirep::where('updated_at', '<', $date)
            ->where('state', PirepState::IN_PROGRESS)
            ->delete();
    }
}
