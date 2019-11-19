<?php

namespace App\Cron\Nightly;

use App\Contracts\Listener;
use App\Events\CronNightly;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

/**
 * This recalculates the balances on all of the journals
 */
class RecalculateStats extends Listener
{
    private $userSvc;

    public function __construct(UserService $userService)
    {
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

        $this->userSvc->recalculateAllUserStats();

        Log::info('Done recalculating stats');
    }
}
