<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Events\UserStateChanged;
use App\Events\UserStatsChanged;
use App\Facades\Utils;
use App\Models\Enums\UserState;
use App\Models\Rank;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AircraftRepository;
use App\Repositories\SubfleetRepository;
use App\Support\Units\Time;
use Illuminate\Support\Collection;
use Log;

class UserService extends BaseService
{
    protected $aircraftRepo, $subfleetRepo;

    /**
     * UserService constructor.
     * @param AircraftRepository $aircraftRepo
     * @param SubfleetRepository $subfleetRepo
     */
    public function __construct(
        AircraftRepository $aircraftRepo,
        SubfleetRepository $subfleetRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->subfleetRepo = $subfleetRepo;
    }

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
     * Return the subfleets this user is allowed access to,
     * based on their current rank
     * @param $user
     * @return Collection
     */
    public function getAllowableSubfleets($user)
    {
        if($user === null || setting('pireps.restrict_aircraft_to_rank') === false) {
            return $this->subfleetRepo->with('aircraft')->all();
        }

        $subfleets = $user->rank->subfleets();
        return $subfleets->with('aircraft')->get();
    }

    /**
     * Return a bool if a user is allowed to fly the current aircraft
     * @param $user
     * @param $aircraft_id
     * @return bool
     */
    public function aircraftAllowed($user, $aircraft_id)
    {
        $aircraft = $this->aircraftRepo->find($aircraft_id, ['subfleet_id']);
        $subfleets = $this->getAllowableSubfleets($user);
        $subfleet_ids = $subfleets->pluck('id')->toArray();

        return \in_array($aircraft->subfleet_id, $subfleet_ids, true);
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

        return $user;
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

        # If their current rank is one they were assigned, then
        # don't change away from it automatically.
        if($user->rank && $user->rank->auto_promote === false) {
            return $user;
        }

        $pilot_hours = new Time($user->flight_time);

        # The current rank's hours are over the pilot's current hours,
        # so assume that they were "placed" here by an admin so don't
        # bother with updating it
        if($user->rank && $user->rank->hours > $pilot_hours->hours) {
            return $user;
        }

        $old_rank = $user->rank;
        $original_rank_id = $user->rank_id;

        $ranks = Rank::where('auto_promote', true)
                    ->orderBy('hours', 'asc')->get();

        foreach ($ranks as $rank) {
            if($rank->hours > $pilot_hours->hours) {
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
