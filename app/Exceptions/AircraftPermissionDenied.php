<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AircraftPermissionDenied
 * @package App\Exceptions
 */
class AircraftPermissionDenied extends HttpException
{
    public function __construct(
        string $message = null,
        \Exception $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct(
            400,
            'User is not allowed to fly this aircraft',
            $previous, $headers, $code
        );
    }
}
