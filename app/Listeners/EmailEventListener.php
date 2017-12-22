<?php

namespace App\Listeners;

use Log;
use Illuminate\Support\Facades\Mail;

use App\Models\Enums\PilotState;
use \App\Events\UserRegistered;

/**
 * Handle sending emails on different events
 * @package App\Listeners
 */
class EmailEventListener
{
    public function subscribe($events)
    {
        $events->listen(
            UserRegistered::class,
            'App\Listeners\EmailEventListener@onUserRegister'
        );
    }

    /**
     * Send an email when the user registered
     * @param UserRegistered $event
     */
    public function onUserRegister(UserRegistered $event)
    {
        Log::info('onUserRegister: '
                  . $event->user->pilot_id . ' is '
                  . PilotState::label($event->user->state)
                  . ', sending active email');

        if($event->user->state === PilotState::ACTIVE) {
            Mail::to($event->user->email)->send(new \App\Mail\UserRegistered($event->user));
        } else if($event->user->state === PilotState::PENDING) {
            Mail::to($event->user->email)->send(new \App\Mail\UserPending($event->user));
        }
    }

}
