<?php

namespace App\Exceptions;

/**
 * Class AircraftPermissionDenied
 * @package App\Exceptions
 */
class AircraftPermissionDenied extends InternalError
{
    public const FIELD = 'aircraft_id';
    public const MESSAGE = 'User is not allowed to fly this aircraft';
}
