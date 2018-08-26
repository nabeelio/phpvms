<?php

namespace App\Cron\Nightly;

use App\Events\CronNightly;
use App\Interfaces\Listener;
use App\Models\Enums\Days;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Figure out what flights need to be active for today
 */
class SetActiveFlights extends Listener
{
    /**
     * @param CronNightly $event
     */
    public function handle(CronNightly $event): void
    {
        $this->checkFlights();
    }

    /**
     * Look through every single flight, check the start/end dates,
     * as well of the days of week if this flight is active on this day
     *
     * TODO: Option to check the flight active/inactive against departure TZ
     * TODO: Move to FlightService
     */
    public function checkFlights(): void
    {
        $today = Carbon::now('UTC');
        $flights = Flight::all();

        /**
         * @var Flight $flight
         */
        foreach ($flights as $flight) {
            if (!$flight->active) {
                continue;
            }

            // dates aren't set, so just save if there were any changes above
            // and move onto the next one
            if ($flight->start_date === null || $flight->end_date === null) {
                if ($flight->days !== null && $flight->days > 0) {
                    $visible = Days::isToday($flight->days);
                    if ($flight->visible !== $visible) {
                        Log::info('Flight '.$flight->ident.' to '.($visible ? 'shown' : 'hidden'));

                        $flight->visible = $visible;
                        if ($visible === false) {
                            Log::info('Today='.date('N').', start=no, mask='.$flight->days.', in='
                                .Days::in($flight->days, Days::$isoDayMap[(int) date('N')]));
                        }
                    }
                } else {
                    Log::info('Toggling flight '.$flight->ident.' to visible');
                    $flight->visible = true;
                }

                $flight->save();
                continue;
            }

            // Check the day of week now first

            // Start/end date is set, so make sure today is valid for it to be alive
            // and then make sure if days of the week are specified, check that too
            if ($today->gte($flight->start_date) && $today->lte($flight->end_date)) {
                if ($flight->days !== null && $flight->days > 0) {
                    $visible = Days::isToday($flight->days);
                    if ($flight->visible !== $visible) {
                        Log::info('Toggling flight '.$flight->ident.' to '.($visible ? 'shown' : 'hidden').'');

                        $flight->visible = $visible;
                        if ($visible === false) {
                            Log::info('Today='.date('N').', start='.$flight->start_date
                                .', end='.$flight->end_date.', mask='.$flight->days.', in='
                                .Days::in($flight->days, Days::$isoDayMap[(int) date('N')]));
                        }
                    }
                } else {
                    Log::info('Toggling flight '.$flight->ident.' to visible');
                    $flight->visible = true;
                }
            } else {
                $flight->visible = false;
                Log::info('Toggling flight '.$flight->ident.' to hidden, outside of start/end boundary');
            }

            $flight->save();
        }
    }
}
