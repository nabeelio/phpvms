<?php

namespace App\Cron\Nightly;

use App\Contracts\Listener;
use App\Events\CronNightly;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;

/**
 * Determine if any pilots should be set to ON LEAVE status
 */
class PilotLeave extends Listener
{
    private $userSvc;

    /**
     * PilotLeave constructor.
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
        if (setting('pilots.auto_leave_days') === 0) {
            return;
        }

        $date = Carbon::now()->subDay(setting('pilots.auto_leave_days'));
        $users = User::where('status', UserState::ACTIVE)
           ->whereDate('updated_at', '<', $date);

        foreach ($users as $user) {
            Log::info('Setting user '.$user->ident.' to ON LEAVE status');
            $this->userSvc->setStatusOnLeave($user);
        }
    }
}
