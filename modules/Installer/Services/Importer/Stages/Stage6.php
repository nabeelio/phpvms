<?php

namespace Modules\Installer\Services\Importer\Stages;

use App\Models\User;
use App\Services\UserService;
use Modules\Installer\Exceptions\StageCompleted;
use Modules\Installer\Services\Importer\BaseStage;

class Stage6 extends BaseStage
{
    public $nextStage = 'complete';

    public function run($start = 0)
    {
        $this->findLastPireps();
        $this->recalculateUserStats();

        throw new StageCompleted($this->nextStage);
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
