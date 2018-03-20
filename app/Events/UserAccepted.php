<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserAccepted
 * @package App\Events
 */
class UserAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    /**
     * UserAccepted constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
