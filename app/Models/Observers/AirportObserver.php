<?php

namespace App\Models\Observers;

use App\Models\Airport;

/**
 * Make sure that the fields are properly capitalized
 */
class AirportObserver
{
    /**
     * @param Airport $airport
     */
    public function creating(Airport $airport): void
    {
        if (filled($airport->iata)) {
            $airport->iata = strtoupper(trim($airport->iata));
        }

        $airport->icao = strtoupper(trim($airport->icao));
        $airport->id = $airport->icao;
    }

    /**
     * @param Airport $airport
     */
    public function updating(Airport $airport): void
    {
        if (filled($airport->iata)) {
            $airport->iata = strtoupper(trim($airport->iata));
        }

        $airport->icao = strtoupper(trim($airport->icao));
        $airport->id = $airport->icao;
    }
}
