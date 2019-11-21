<?php

namespace Modules\Installer\Services\Importer\Stages;

use App\Models\User;
use App\Services\UserService;
use Modules\Installer\Services\Importer\BaseStage;

class Stage5 extends BaseStage
{
    /**
     * @override
     */
    public function run()
    {
        $this->findLastPireps();
        $this->recalculateUserStats();
    }

    /**
     * Go through and set the last PIREP ID for the users
     */
    protected function findLastPireps()
    {
    }

    /**
     * Recalculate all of the user stats
     */
    protected function recalculateUserStats()
    {
        $this->comment('--- RECALCULATING USER STATS ---');
        $userSvc = app(UserService::class);

        User::all()->each(function ($user) use ($userSvc) {
            $userSvc->recalculateStats($user);
        });
    }
}
