<?php

namespace App\Listeners;

use App\Events\UserStatsChanged;
use App\Contracts\Listener;
use App\Models\Award;

/**
 * Look for and run any of the award classes. Don't modify this.
 * See the documentation on creating awards:
 *
 * @url http://docs.phpvms.net/customizing/awards
 */
class AwardListener extends Listener
{
    /**
     * Call all of the awards
     *
     * @param UserStatsChanged $event
     */
    public function handle(UserStatsChanged $event): void
    {
        $awards = Award::all();
        foreach ($awards as $award) {
            $klass = $award->getReference($award, $event->user);
            if ($klass) {
                $klass->handle();
            }
        }
    }
}
