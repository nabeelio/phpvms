<?php

namespace App\Events;

use App\Contracts\Event;
use App\Models\UserAward;

class AwardAwarded extends Event
{
    public UserAward $userAward;

    public function __construct(UserAward $userAward)
    {
        $this->userAward = $userAward;
    }
}
