<?php

namespace App\Services;

use App\Contracts\Service;
use App\Events\UserRegistered;
use App\Events\UserStateChanged;
use App\Events\UserStatsChanged;
use App\Exceptions\UserPilotIdExists;
use App\Models\Enums\PirepState;
use App\Models\Enums\UserState;
use App\Models\Pirep;
use App\Models\Rank;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AircraftRepository;
use App\Repositories\SubfleetRepository;
use App\Repositories\UserRepository;
use App\Support\Units\Time;
use App\Support\Utils;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use function is_array;

class UserService extends Service
{
    private $aircraftRepo;
    private $subfleetRepo;
    private $userRepo;

    /**
     * UserService constructor.
     *
     * @param AircraftRepository $aircraftRepo
     * @param SubfleetRepository $subfleetRepo
     * @param UserRepository     $userRepo
     */
    public function __construct(
        AircraftRepository $aircraftRepo,
        SubfleetRepository $subfleetRepo,
        UserRepository $userRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->subfleetRepo = $subfleetRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Register a pilot. Also attaches the initial roles
     * required, and then triggers the UserRegistered event
     *
     * @param array $attrs Array with the user data
     * @param array $roles List of "display_name" of groups to assign
     *
     * @throws \Exception
     *
     * @return User
     */
    public function createUser(array $attrs, array $roles = null): User
    {
        $user = User::create($attrs);
        $user->api_key = Utils::generateApiKey();
        $user->curr_airport_id = $user->home_airport_id;

        // Determine if we want to auto accept
        if (setting('pilots.auto_accept') === true) {
            $user->state = UserState::ACTIVE;
        } else {
            $user->state = UserState::PENDING;
        }

        $user->save();

        // Attach any additional roles
        if (!empty($roles) && is_array($roles)) {
            foreach ($roles as $role) {
                $this->addUserToRole($user, $role);
            }
        }

        // Let's check their rank and where they should start
        $this->calculatePilotRank($user);
        $user->refresh();

        event(new UserRegistered($user));

        return $user;
    }

    /**
     * Add a user to a given role
     *
     * @param User   $user
     * @param string $roleName
     *
     * @return User
     */
    public function addUserToRole(User $user, $roleName): User
    {
        $role = Role::where(['name' => $roleName])->first();
        $user->attachRole($role);

        return $user;
    }

    /**
     * Find and return the next available pilot ID (usually just the max+1)
     *
     * @return int
     */
    public function getNextAvailablePilotId(): int
    {
        return (int) User::max('pilot_id') + 1;
    }

    /**
     * Find the next available pilot ID and set the current user's pilot_id to that +1
     * Called from UserObserver right now after a record is created
     *
     * @param User $user
     *
     * @return User
     */
    public function findAndSetPilotId(User $user): User
    {
        if ($user->pilot_id !== null && $user->pilot_id > 0) {
            return $user;
        }

        $user->pilot_id = $this->getNextAvailablePilotId();
        $user->save();

        Log::info('Set pilot ID for user '.$user->id.' to '.$user->pilot_id);

        return $user;
    }

    public function isPilotIdAlreadyUsed(int $pilot_id): bool
    {
        return User::where('pilot_id', '=', $pilot_id)->exists();
    }

    /**
     * Change a user's pilot ID
     *
     * @param User $user
     * @param int  $pilot_id
     *
     * @throws UserPilotIdExists
     *
     * @return User
     */
    public function changePilotId(User $user, int $pilot_id): User
    {
        if ($user->pilot_id === $pilot_id) {
            return $user;
        }

        if ($this->isPilotIdAlreadyUsed($pilot_id)) {
            Log::error('User with id '.$pilot_id.' already exists');

            throw new UserPilotIdExists($user);
        }

        $old_id = $user->pilot_id;
        $user->pilot_id = $pilot_id;
        $user->save();

        Log::info('Changed pilot ID for user '.$user->id.' from '.$old_id.' to '.$user->pilot_id);

        return $user;
    }

    /**
     * Return the subfleets this user is allowed access to,
     * based on their current rank
     *
     * @param $user
     *
     * @return Collection
     */
    public function getAllowableSubfleets($user)
    {
        if ($user === null || setting('pireps.restrict_aircraft_to_rank') === false) {
            return $this->subfleetRepo->with('aircraft')->all();
        }

        $subfleets = $user->rank->subfleets();
        return $subfleets->with('aircraft')->get();
    }

    /**
     * Return a bool if a user is allowed to fly the current aircraft
     *
     * @param $user
     * @param $aircraft_id
     *
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
     *
     * @param User $user
     * @param      $old_state
     *
     * @return User
     */
    public function changeUserState(User $user, $old_state): User
    {
        if ($user->state === $old_state) {
            return $user;
        }

        Log::info('User '.$user->ident.' state changing from '
            .UserState::label($old_state).' to '
            .UserState::label($user->state));

        event(new UserStateChanged($user, $old_state));

        return $user;
    }

    /**
     * Adjust the number of flights a user has. Triggers
     * UserStatsChanged event
     *
     * @param User $user
     * @param int  $count
     *
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
     *
     * @param User $user
     * @param int  $minutes
     *
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
     *
     * @param User $user
     *
     * @return User
     */
    public function calculatePilotRank(User $user): User
    {
        $user->refresh();

        // If their current rank is one they were assigned, then
        // don't change away from it automatically.
        if ($user->rank && $user->rank->auto_promote === false) {
            return $user;
        }

        // If we should count their transfer hours?
        if (setting('pilots.count_transfer_hours', false) === true) {
            $pilot_hours = new Time($user->flight_time + $user->transfer_time);
        } else {
            $pilot_hours = new Time($user->flight_time);
        }

        // The current rank's hours are over the pilot's current hours,
        // so assume that they were "placed" here by an admin so don't
        // bother with updating it
        if ($user->rank && $user->rank->hours > $pilot_hours->hours) {
            return $user;
        }

        $old_rank = $user->rank;
        $original_rank_id = $user->rank_id;

        $ranks = Rank::where('auto_promote', true)
            ->orderBy('hours', 'asc')->get();

        foreach ($ranks as $rank) {
            if ($rank->hours > $pilot_hours->hours) {
                break;
            }

            $user->rank_id = $rank->id;
        }

        // Only trigger the event/update if there's been a change
        if ($user->rank_id !== $original_rank_id) {
            $user->save();
            $user->refresh();
            event(new UserStatsChanged($user, 'rank', $old_rank));
        }

        return $user;
    }

    /**
     * Set the user's status to being on leave
     *
     * @param User $user
     *
     * @return User
     */
    public function setStatusOnLeave(User $user): User
    {
        $user->refresh();
        $user->state = UserState::ON_LEAVE;
        $user->save();

        event(new UserStateChanged($user, UserState::ACTIVE));

        $user->refresh();
        return $user;
    }

    /**
     * Recalculate the stats for all active users
     */
    public function recalculateAllUserStats(): void
    {
        $w = [
            ['state', '!=', UserState::REJECTED],
        ];

        $this->userRepo
            ->findWhere($w, ['id', 'name', 'airline_id'])
            ->each(function ($user, $_) {
                return $this->recalculateStats($user);
            });
    }

    /**
     * Recount/update all of the stats for a user
     *
     * @param User $user
     *
     * @return User
     */
    public function recalculateStats(User $user): User
    {
        // Recalc their hours
        $w = [
            'user_id' => $user->id,
            'state'   => PirepState::ACCEPTED,
        ];

        $pirep_count = Pirep::where($w)->count();
        $user->flights = $pirep_count;

        $flight_time = Pirep::where($w)->sum('flight_time');
        $user->flight_time = $flight_time;

        $user->save();

        // Recalc the rank
        $this->calculatePilotRank($user);

        Log::info('User '.$user->ident.' updated; pirep count='.$pirep_count
            .', rank='.$user->rank->name
            .', flight_time='.$user->flight_time.' minutes');

        $user->save();
        return $user;
    }
}
