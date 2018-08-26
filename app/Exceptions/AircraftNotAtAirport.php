<?php

namespace App\Exceptions;

/**
 * Class AircraftNotAtAirport
 */
class AircraftNotAtAirport extends InternalError
{
    public const FIELD = 'aircraft_id';
    public const MESSAGE = 'The aircraft is not at the departure airport';
}
