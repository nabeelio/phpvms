<?php

namespace App\Exceptions;

use App\Models\Aircraft;

class AircraftNotAvailable extends AbstractHttpException
{
    public const MESSAGE = 'The aircraft is not available for flight';

    public function __construct(
        private readonly Aircraft $aircraft
    ) {
        parent::__construct(
            400,
            static::MESSAGE
        );
    }

    public function getErrorType(): string
    {
        return 'aircraft-not-available';
    }

    public function getErrorDetails(): string
    {
        return $this->getMessage();
    }

    public function getErrorMetadata(): array
    {
        return [
            'aircraft_id' => $this->aircraft->id,
        ];
    }
}
