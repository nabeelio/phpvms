<?php

namespace App\Cron\Nightly;

use App\Contracts\Listener;
use App\Events\CronNightly;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Determine if any pilots should be set to ON LEAVE status
 */
class PilotLeave extends Listener
{
    private $userSvc;

    /**
     * PilotLeave constructor.
     *
     * @param UserService $userSvc
     */
    public function __construct(UserService $userSvc)
    {
        $this->userSvc = $userSvc;
    }

    /**
     * Set any users to being on leave after X days
     *
     * @param CronNightly $event
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function handle(CronNightly $event): void
    {
        $users = $this->userSvc->findUsersOnLeave();
        foreach ($users as $user) {
            Log::info('Setting user '.$user->ident.' to ON LEAVE status');
            $this->userSvc->setStatusOnLeave($user);
        }
    }
}
