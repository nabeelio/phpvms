<?php

namespace App\Exceptions;

/**
 * Class AircraftPermissionDenied
 */
class AircraftPermissionDenied extends InternalError
{
    public const FIELD = 'aircraft_id';
    public const MESSAGE = 'User is not allowed to fly this aircraft';
}
