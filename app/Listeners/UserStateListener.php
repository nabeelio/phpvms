<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepFiled;
use App\Events\UserStateChanged;
use App\Models\Enums\UserState;

class UserStateListener extends Listener
{
    public function handle(PirepFiled $event): void
    {
        // Check the user state, set them to ACTIVE if on leave
        if ($event->pirep->user->state !== UserState::ACTIVE) {
            $old_state = $event->pirep->user->state;
            $event->pirep->user->state = UserState::ACTIVE;
            $event->pirep->user->save();

            event(new UserStateChanged($event->pirep->user, $old_state));
        }
    }
}
