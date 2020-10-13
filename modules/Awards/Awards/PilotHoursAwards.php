<?php

namespace Modules\Awards\Awards;

use App\Contracts\Award;

/**
 * All award classes need to extend Award and implement the check() method
 * This award is based on the original PilotFlightAwards.php file but
 * checks the Pilots Flight Time (In Minutes).
 * This award means you can create an award for a pilot that completes any
 * amount of flight time (In Minutes).
 *
 * See: https://docs.phpvms.net/developers/awards
 */
class PilotHoursAwards extends Award
{
    /**
     * Set the name of this award class to make it easier to see when
     * assigning to a specific award
     *
     * @var string
     */
    public $name = 'Pilot Flight Time';

    /**
     * The description to show under the parameters field, so the admin knows
     * what the parameter actually controls. You can leave this blank if there
     * isn't a parameter.
     *
     * @var string
     */
    public $param_description = 'Amount of flight time in Minutes at which to give this award';

    /**
     * If the user has over N minutes of flights, then we can give them this award.
     *
     * If no parameter is passed in, just default it to 99999. You should check if there
     * is a parameter or not. You can call it whatever you want, since that would make
     * sense with the $param_description.
     *
     * @param int|null $flight_minutes The parameters passed in from the UI
     *
     * @return bool
     */
    public function check($flight_minutes = null): bool
    {
        if (!$flight_minutes) {
            $flight_minutes = 99999;
        }
        if (!is_int($flight_minutes)) {
            return false;
        }

        return $this->user->flight_time >= (int) $flight_minutes;
    }
}
