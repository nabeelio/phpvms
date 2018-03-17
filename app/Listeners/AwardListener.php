<?php

namespace App\Listeners;

use App\Events\UserStatsChanged;
use App\Models\Award;

/**
 * Look for and run any of the award classes
 * @package App\Listeners
 */
class AwardListener
{
    /**
     * Call all of the awards
     * @param UserStatsChanged $event
     */
    public function handle(UserStatsChanged $event)
    {
        $awards = Award::all();
        foreach($awards as $award) {
            $klass = $award->getReference($award, $event->user);
            if($klass) {
                $klass->handle();
            }
        }
    }
}
