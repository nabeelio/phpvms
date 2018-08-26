<?php

namespace App\Exceptions;

/**
 * Class UserNotAtAirport
 */
class UserNotAtAirport extends InternalError
{
    public const FIELD = 'dpt_airport_id';
    public const MESSAGE = 'Pilot is not at the departure airport';
}
