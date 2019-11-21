<?php

namespace Modules\Installer\Services\Importer\Importers;

use App\Facades\Utils;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Installer\Services\Importer\BaseImporter;

/**
 * Class UserImport
 */
class UserImport extends BaseImporter
{
    /**
     * @var UserService
     */
    private $userSvc;

    public function run()
    {
        $this->comment('--- USER IMPORT ---');

        $this->userSvc = app(UserService::class);

        $count = 0;
        $first_row = true;
        foreach ($this->db->readRows('pilots', 50) as $row) {
            $pilot_id = $row->pilotid; // This isn't their actual ID
            $name = $row->firstname.' '.$row->lastname;

            // Figure out which airline, etc, they belong to
            $airline_id = $this->idMapper->getMapping('airlines', $row->code);
            $rank_id = $this->idMapper->getMapping('ranks', $row->rank);
            $state = $this->getUserState($row->retired);

            if ($first_row) {
                $new_password = 'admin';
                $first_row = false;
            } else {
                $new_password = Str::random(60);
            }

            // Look for a user with that pilot ID already. If someone has it
            if ($this->userSvc->isPilotIdAlreadyUsed($pilot_id)) {
                Log::info('User with pilot id '.$pilot_id.' exists, reassigning');
                $pilot_id = $this->userSvc->getNextAvailablePilotId();
            }

            $attrs = [
                'pilot_id'        => $pilot_id,
                'name'            => $name,
                'password'        => Hash::make($new_password),
                'api_key'         => Utils::generateApiKey(),
                'airline_id'      => $airline_id,
                'rank_id'         => $rank_id,
                'home_airport_id' => $row->hub,
                'curr_airport_id' => $row->hub,
                'flights'         => (int) $row->totalflights,
                'flight_time'     => Utils::hoursToMinutes($row->totalhours),
                'state'           => $state,
                'created_at'      => $this->parseDate($row->joindate),
            ];

            $user = User::updateOrCreate(['email' => $row->email], $attrs);
            $this->idMapper->addMapping('users', $row->pilotid, $user->id);

            $this->updateUserRoles($user, $row->pilotid);

            if ($user->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' users');
    }

    /**
     * Update a user's roles and add them to the proper ones
     *
     * @param User   $user
     * @param string $old_pilot_id
     */
    protected function updateUserRoles(User $user, $old_pilot_id)
    {
        // Be default add them to the user role, and then determine if they
        // belong to any other groups, and add them to that
        $this->userSvc->addUserToRole($user, 'user');

        // Figure out what other groups they belong to
    }

    /**
     * Get the user's new state from their original state
     *
     * @param $state
     *
     * @return int
     */
    protected function getUserState($state)
    {
        // TODO: This state might differ between simpilot and classic version

        $state = (int) $state;

        // Declare array of classic states
        $phpvms_classic_states = [
            'ACTIVE'   => 0,
            'INACTIVE' => 1,
            'BANNED'   => 2,
            'ON_LEAVE' => 3,
        ];

        // Decide which state they will be in accordance with v7
        if ($state === $phpvms_classic_states['ACTIVE']) {
            return UserState::ACTIVE;
        }

        if ($state === $phpvms_classic_states['INACTIVE']) {
            // TODO: Make an inactive state?
            return UserState::REJECTED;
        }

        if ($state === $phpvms_classic_states['BANNED']) {
            return UserState::SUSPENDED;
        }

        if ($state === $phpvms_classic_states['ON_LEAVE']) {
            return UserState::ON_LEAVE;
        }

        $this->error('Unknown status: '.$state);

        return UserState::ACTIVE;
    }
}
