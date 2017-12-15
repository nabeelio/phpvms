<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Facades\Utils;
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
     * @param array $data
     * @return mixed
     */
    public function createPilot(array $data)
    {
        $opts = [
            'name' => $data['name'],
            'email' => $data['email'],
            'api_key' => Utils::generateApiKey(),
            'airline_id' => $data['airline'],
            'home_airport_id' => $data['home_airport'],
            'curr_airport_id' => $data['home_airport'],
            'password' => Hash::make($data['password'])
        ];

        # Determine if we want to auto accept
        if(setting('pilot.auto_accept') === true) {
            $opts['status'] = config('enums.states.ACTIVE');
        } else {
            $opts['status'] = config('enums.states.PENDING');
        }

        $user = User::create($opts);

        # Attach the user roles
        $role = Role::where('name', 'user')->first();
        $user->attachRole($role);

        # Let's check their rank
        $this->calculatePilotRank($user);

        # TODO: Send out an email
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
