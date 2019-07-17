<?php

namespace App\Exceptions;

class UserPilotIdExists extends InternalError
{
    public const FIELD = 'pilot_id';
    public const MESSAGE = 'A user with this pilot ID already exists';
}
