<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserStatsChanged
 */
class UserStatsChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stat_name;
    public $old_value;
    public $user;

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
