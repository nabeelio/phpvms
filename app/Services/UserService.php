<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Facades\Utils;
use App\Models\Enums\PilotState;
use App\Models\User;
use App\Models\Rank;
use App\Models\Role;

use App\Events\UserStateChanged;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{

    /**
     * Register a pilot
     * @param User $user
     * @return mixed
     */
    public function createPilot(User $user)
    {
        # Determine if we want to auto accept
        if(setting('pilot.auto_accept') === true) {
            $user->state = PilotState::ACTIVE;
        } else {
            $user->state = PilotState::PENDING;
        }

        $user->save();

        # Attach the user roles
        $role = Role::where('name', 'user')->first();
        $user->attachRole($role);

        # Let's check their rank
        $this->calculatePilotRank($user);

        $user->refresh();

        event(new UserRegistered($user));

        return $user;
    }

    public function adjustFlightCount(User $user, int $count): User
    {
        $user->refresh();
        $user->flights += $count;
        $user->save();

        event(new UserStateChanged($user));

        return $user;
    }

    public function adjustFlightTime(User $user, int $minutes): User
    {
        $user->refresh();
        $user->flight_time += $minutes;
        $user->save();

        event(new UserStateChanged($user));

        return $user;
    }

    public function calculatePilotRank(User $user): User
    {
        $user->refresh();
        $pilot_hours = Utils::minutesToHours($user->flight_time);

        # TODO: Cache
        $ranks = Rank::where('auto_promote', true)->orderBy('hours', 'asc')->get();

        foreach ($ranks as $rank) {
            if($rank->hours > $pilot_hours) {
                break;
            } else {
                $user->rank_id = $rank->id;
            }
        }

        $user->save();

        event(new UserStateChanged($user));

        return $user;
    }
}
