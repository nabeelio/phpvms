<?php

namespace App\Exceptions;

use App\Models\User;

class UserPilotIdExists extends AbstractHttpException
{
    public const MESSAGE = 'A user with this pilot ID already exists';

    private $user;

    public function __construct(User $user)
    {
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
        return 'pilot-id-already-exists';
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
            'user_id' => $this->user->id,
        ];
    }
}
