<?php

namespace App\Cron\Hourly;

use App\Contracts\Listener;
use App\Events\CronHourly;
use App\Services\SimBriefService;
use Illuminate\Support\Facades\Log;

/**
 * Clear any expired SimBrief flight briefs that aren't attached to a PIREP
 */
class ClearExpiredSimbrief extends Listener
{
    private $simbriefSvc;

    public function __construct(SimBriefService $simbriefSvc)
    {
        $this->simbriefSvc = $simbriefSvc;
    }

    /**
     * @param \App\Events\CronNightly $event
     */
    public function handle(CronHourly $event): void
    {
        Log::info('Hourly: Removing expired Simbrief entries');
        $this->simbriefSvc->removeExpiredEntries();
    }
}
