<?php

namespace App\Models\Observers;

use App\Models\Aircraft;
use App\Support\ICAO;

class AircraftObserver
{
    /**
     * @param Aircraft $aircraft
     *
     * @throws \Exception
     */
    public function creating(Aircraft $aircraft): void
    {
        if (!empty($aircraft->airport_id)) {
            $aircraft->airport_id = strtoupper($aircraft->airport_id);
        }

        if (empty($aircraft->hex_code)) {
            $aircraft->hex_code = ICAO::createHexCode();
        }
    }
}
