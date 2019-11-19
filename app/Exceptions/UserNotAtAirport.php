<?php

namespace App\Exceptions;

use App\Models\Airport;
use App\Models\User;

class UserNotAtAirport extends AbstractHttpException
{
    public const MESSAGE = 'Pilot is not at the departure airport';

    private $airport;
    private $user;

    public function __construct(User $user, Airport $airport)
    {
        $this->airport = $airport;
        $this->user = $user;

        parent::__construct(
            400,
            static::MESSAGE
        );
    }

    /**
     * Return the RFC 7807 error type (without the URL root)
     */
    public function getErrorType(): string
    {
        return 'user-not-at-airport';
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
        return [
            'airport_id' => $this->airport->id,
            'user_id'    => $this->user->id,
        ];
    }
}
