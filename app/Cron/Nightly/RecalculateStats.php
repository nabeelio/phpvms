<?php

namespace App\Cron\Nightly;

use App\Contracts\Listener;
use App\Events\CronNightly;
use App\Services\AircraftService;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

/**
 * This recalculates the balances on all of the journals
 */
class RecalculateStats extends Listener
{
    private AircraftService $aircraftSvc;
    private UserService $userSvc;

    public function __construct(AircraftService $aircraftSvc, UserService $userSvc)
    {
        $this->aircraftSvc = $aircraftSvc;
        $this->userSvc = $userSvc;
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
        Log::info('Nightly: Recalculating user stats');
        $this->userSvc->recalculateAllUserStats();

        Log::info('Nightly: Recalcuating aircraft status');
        $this->aircraftSvc->recalculateStats();
    }
}
