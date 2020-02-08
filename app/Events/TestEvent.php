<?php

namespace App\Events;

use App\Models\User;

class TestEvent extends BaseEvent
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
