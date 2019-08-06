<?php

namespace App\Cron\Nightly;

use App\Contracts\Listener;
use App\Events\CronNightly;
use App\Services\VersionService;

/**
 * Determine if any pilots should be set to ON LEAVE status
 */
class NewVersionCheck extends Listener
{
    private $versionSvc;

    /**
     * @param VersionService $versionSvc
     */
    public function __construct(VersionService $versionSvc)
    {
        $this->versionSvc = $versionSvc;
    }

    /**
     * Set any users to being on leave after X days
     *
     * @param CronNightly $event
     */
    public function handle(CronNightly $event): void
    {
        $this->versionSvc->isNewVersionAvailable();
    }
}
