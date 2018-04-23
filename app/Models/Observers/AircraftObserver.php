<?php

namespace App\Models\Observers;

use App\Models\Aircraft;
use App\Support\ICAO;

/**
 * Class AircraftObserver
 * @package App\Models\Observers
 */
class AircraftObserver
{
    /**
     * @param Aircraft $aircraft
     */
    public function creating(Aircraft $aircraft): void
    {
        if (empty($aircraft->hex_code)) {
            $aircraft->hex_code = ICAO::createHexCode();
        }
    }
}
