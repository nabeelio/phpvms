<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepAccepted;
use App\Events\UserStateChanged;
use App\Events\UserStatsChanged;
use App\Models\Award;

/**
 * Look for and run any of the award classes. Don't modify this.
 * See the documentation on creating awards:
 *
 * @url http://docs.phpvms.net/customizing/awards
 */
class AwardHandler extends Listener
{
    /** The events and the callback */
    public static $callbacks = [
        PirepAccepted::class    => 'onPirepAccept',
        UserStatsChanged::class => 'onUserStatsChanged',
        UserStateChanged::class => 'onUserStateChanged',
    ];

    /**
     * Called when a PIREP is accepted
     *
     * @param \App\Events\PirepAccepted $event
     */
    public function onPirepAccept(PirepAccepted $event)
    {
        $this->checkForAwards($event->pirep->user);
    }

    /**
     * When the user's state has changed
     *
     * @param \App\Events\UserStateChanged $event
     */
    public function onUserStateChanged(UserStateChanged $event): void
    {
        $this->checkForAwards($event->user);
    }

    /**
     * Called when any of the user's states have changed
     *
     * @param UserStatsChanged $event
     */
    public function onUserStatsChanged(UserStatsChanged $event): void
    {
        $this->checkForAwards($event->user);
    }

    /**
     * Check for any awards to be run and test them against the user
     *
     * @param \App\Models\User $user
     */
    public function checkForAwards($user)
    {
        $awards = Award::all();
        foreach ($awards as $award) {
            $klass = $award->getReference($award, $user);
            if ($klass) {
                $klass->handle();
            }
        }
    }
}
