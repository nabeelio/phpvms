<?php

namespace App\Events;

use App\Models\User;

class UserAccepted extends BaseEvent
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
