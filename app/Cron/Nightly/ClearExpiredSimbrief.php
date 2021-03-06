<?php

namespace App\Cron\Nightly;

use App\Contracts\Listener;
use App\Events\CronNightly;
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
    public function handle(CronNightly $event): void
    {
        Log::info('Nightly: Removing expired Simbrief entries');
        $this->simbriefSvc->removeExpiredEntries();
    }
}
