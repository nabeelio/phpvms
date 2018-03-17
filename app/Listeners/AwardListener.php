<?php

namespace App\Listeners;

use App\Events\UserStateChanged;
use App\Models\Award;

/**
 * Look for and run any of the award classes
 * @package App\Listeners
 */
class AwardListener
{
    /**
     * Call all of the awards
     * @param UserStateChanged $event
     */
    public function handle(UserStateChanged $event)
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
