<?php

namespace App\Events;

use App\Contracts\Event;
use App\Models\User;

/**
 * Event triggered when a user's state changes
 */
class UserStateChanged extends Event
{
    public $old_state;
    public $user;

    public function __construct(User $user, $old_state)
    {
        $this->old_state = $old_state;
        $this->user = $user;
    }
}
