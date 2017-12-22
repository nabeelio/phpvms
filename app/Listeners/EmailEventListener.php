<?php

namespace App\Listeners;

use App\Events\UserStateChanged;
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
            $email = new \App\Mail\UserRegistered(
                $event->user,
                'Welcome to ' . config('app.name') . '!'
            );

            Mail::to($event->user->email)->send($email);
        } else if($event->user->state === PilotState::PENDING) {
            Mail::to($event->user->email)->send(new \App\Mail\UserPending($event->user));
        }
    }

    /**
     * When a user's state changes, send an email out
     * @param UserStateChanged $event
     */
    public function onUserStateChange(UserStateChanged $event)
    {
        if ($event->old_state === PilotState::PENDING) {
            if ($event->user->state === PilotState::ACTIVE)
            {
                $email = new \App\Mail\UserRegistered(
                    $event->user,
                    'Your registration has been accepted!'
                );

                Mail::to($event->user->email)->send($email);
            }

            else if ($event->user->state === PilotState::REJECTED)
            {
                $email = new \App\Mail\UserRejected($event->user);
                Mail::to($event->user->email)->send($email);
            }
        }

        # TODO: Other state transitions
        elseif ($event->old_state === PilotState::ACTIVE)
        {

        }
    }
}
