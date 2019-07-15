<?php

namespace App\Cron\Nightly;

use App\Contracts\Listener;
use App\Events\CronNightly;
use App\Models\Enums\UserState;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Log;

/**
 * This recalculates the balances on all of the journals
 */
class RecalculateStats extends Listener
{
    private $userRepo;
    private $userSvc;

    public function __construct(UserRepository $userRepo, UserService $userService)
    {
        $this->userRepo = $userRepo;
        $this->userSvc = $userService;
    }

    /**
     * Recalculate the stats for active users
     *
     * @param CronNightly $event
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function handle(CronNightly $event): void
    {
        Log::info('Recalculating balances');

        $w = [
            ['state', '!=', UserState::REJECTED],
        ];

        $users = $this->userRepo->findWhere($w, ['id', 'name', 'airline_id']);
        foreach ($users as $user) {
            $this->userSvc->recalculateStats($user);
        }

        Log::info('Done recalculating stats');
    }
}
