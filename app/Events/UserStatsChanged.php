<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserStatsChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stat_name,
           $old_value,
           $user;

    /*
     * When a user's stats change. Stats changed match the field name:
     *      airport
     *      flights
     *      rank
     */
    public function __construct(User $user, $stat_name, $old_value)
    {
        $this->user = $user;
        $this->stat_name = $stat_name;
        $this->old_value = $old_value;
    }
}
