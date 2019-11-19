<?php

namespace App\Exceptions;

class AirportNotFound extends AbstractHttpException
{
    private $icao;

    public function __construct($icao)
    {
        $this->icao = $icao;
        parent::__construct(
            404,
            'Airport '.$icao.' not found'
        );
    }

    /**
     * Return the RFC 7807 error type (without the URL root)
     */
    public function getErrorType(): string
    {
        return 'airport-not-found';
    }

    /**
     * Get the detailed error string
     */
    public function getErrorDetails(): string
    {
        return $this->getMessage();
    }

    /**
     * Return an array with the error details, merged with the RFC7807 response
     */
    public function getErrorMetadata(): array
    {
        return [];
    }
}
