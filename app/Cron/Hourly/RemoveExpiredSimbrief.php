<?php

namespace App\Cron\Hourly;

use App\Contracts\Listener;
use App\Events\CronHourly;
use App\Services\SimBriefService;
use Illuminate\Support\Facades\Log;

/**
 * Remove any expired SimBrief flight briefs that aren't used
 * (No active flights, no PIREPs)
 */
class RemoveExpiredSimbrief extends Listener
{
    private $simbriefSvc;

    public function __construct(SimBriefService $simbriefSvc)
    {
        $this->simbriefSvc = $simbriefSvc;
    }

    /**
     * @param \App\Events\CronHourly $event
     */
    public function handle(CronHourly $event): void
    {
        Log::info('Hourly: Removing expired Simbrief entries');
        $this->simbriefSvc->removeExpiredBriefings();
    }
}
