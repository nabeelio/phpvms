<?php

namespace App\Services;

use App\Facades\Utils;
use App\Models\User;
use App\Models\Rank;
use App\Models\Role;
use App\Events\UserRegistered;
use App\Events\UserStateChanged;
use App\Events\UserStatsChanged;
use App\Models\Enums\UserState;

class UserService extends BaseService
{
    /**
     * Register a pilot. Also attaches the initial roles
     * required, and then triggers the UserRegistered event
     * @param User $user        User model
     * @param array $groups     Additional groups to assign
     * @return mixed
     */
    public function createPilot(User $user, array $groups=null)
    {
        # Determine if we want to auto accept
        if(setting('pilot.auto_accept') === true) {
            $user->state = UserState::ACTIVE;
        } else {
            $user->state = UserState::PENDING;
        }

        $user->save();

        # Attach the user roles
        $role = Role::where('name', 'user')->first();
        $user->attachRole($role);

        if(!empty($groups) && \is_array($groups)) {
            foreach ($groups as $group) {
                $role = Role::where('name', $group)->first();
                $user->attachRole($role);
            }
        }

        # Let's check their rank and where they should start
        $this->calculatePilotRank($user);

        $user->refresh();

        event(new UserRegistered($user));

        return $user;
    }

    /**
     * Change the user's state. PENDING to ACCEPTED, etc
     * Send out an email
     * @param User $user
     * @param $old_state
     * @return User
     */
    public function changeUserState(User $user, $old_state): User
    {
        if($user->state === $old_state) {
            return $user;
        }

        Log::info('User ' . $user->pilot_id . ' state changing from '
                  . UserState::label($old_state) . ' to '
                  . UserState::label($user->state));

        event(new UserStateChanged($user, $old_state));
    }

    /**
     * Adjust the number of flights a user has. Triggers
     * UserStatsChanged event
     * @param User $user
     * @param int $count
     * @return User
     */
    public function adjustFlightCount(User $user, int $count): User
    {
        $user->refresh();
        $old_value = $user->flights;
        $user->flights += $count;
        $user->save();

        event(new UserStatsChanged($user, 'flights', $old_value));

        return $user;
    }

    /**
     * Update a user's flight times
     * @param User $user
     * @param int $minutes
     * @return User
     */
    public function adjustFlightTime(User $user, int $minutes): User
    {
        $user->refresh();
        $user->flight_time += $minutes;
        $user->save();

        return $user;
    }


    /**
     * See if a pilot's rank has change. Triggers the UserStatsChanged event
     * @param User $user
     * @return User
     */
    public function calculatePilotRank(User $user): User
    {
        $user->refresh();
        $old_rank = $user->rank;
        $original_rank_id = $user->rank_id;
        $pilot_hours = Utils::minutesToHours($user->flight_time);

        # TODO: Cache
        $ranks = Rank::where('auto_promote', true)
                    ->orderBy('hours', 'asc')->get();

        foreach ($ranks as $rank) {
            if($rank->hours > $pilot_hours) {
                break;
            } else {
                $user->rank_id = $rank->id;
            }
        }

        // Only trigger the event/update if there's been a change
        if($user->rank_id !== $original_rank_id) {
            $user->save();
            $user->refresh();
            event(new UserStatsChanged($user, 'rank', $old_rank));
        }

        return $user;
    }

    /**
     * Recount/update all of the stats for a user
     * @param User $user
     * @return User
     */
    public function recalculateStats(User $user): User
    {
        return $user;
    }
}
