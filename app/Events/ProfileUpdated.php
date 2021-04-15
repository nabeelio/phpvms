<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $avatarUpdated;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param bool $avatarUpdated
     */
    public function __construct(User $user, bool $avatarUpdated)
    {
        $this->user = $user;
        $this->avatarUpdated = $avatarUpdated;
    }
}
