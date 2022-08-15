<?php

namespace App\Events;

use App\Contracts\Event;
use App\Models\User;

class TestEvent extends Event
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
