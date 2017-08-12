<?php

namespace App\Services;

use App\Models\User;
use App\Models\Rank;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class PilotService extends BaseService
{

    public function adjustFlightCount(User &$pilot, int $count): User
    {
        $pilot->refresh();
        $pilot->flights = $pilot->flights + $count;
        $pilot->save();

        return $pilot;
    }

    public function adjustFlightHours(User &$pilot, int $hours): User
    {
        $pilot->refresh();
        $pilot->flight_time = $pilot->flight_time + $hours;
        $pilot->save();

        return $pilot;
    }

    public function calculatePilotRank(User &$pilot): User
    {
        $pilot->refresh();
        $pilot_hours = $pilot->flight_time / 3600;

        # TODO: Cache
        $ranks = Cache::remember(
            config('cache.keys.RANKS_PILOT_LIST.key'),
            config('cache.keys.RANKS_PILOT_LIST.time'),
            function () {
                return Rank::where('auto_promote', true)->orderBy('hours', 'asc')->get();
            });

        foreach ($ranks as $rank) {
            if($rank->hours > $pilot_hours) {
                break;
            } else {
                $pilot->rank_id = $rank->id;
            }
        }

        $pilot->save();

        return $pilot;
    }

    public function createPilot(array $data)
    {
        $user = User::create(['name' => $data['name'],
                              'email' => $data['email'],
                              'airline_id' => $data['airline'],
                              'home_airport_id' => $data['home_airport'],
                              'curr_airport_id' => $data['home_airport'],
                              'password' => Hash::make($data['password'])]);
        # Attach the user roles
        $role = Role::where('name', 'user')->first();
        $user->attachRole($role);
        # Let's check their rank
        $this->calculatePilotRank($user);
        # TODO: Send out an email

        # Looking good, let's return their information
        return $user;
    }

}
