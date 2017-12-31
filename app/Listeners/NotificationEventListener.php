<?php

namespace App\Listeners;

use Log;
use Illuminate\Support\Facades\Mail;

use App\Events\UserRegistered;
use App\Models\Enums\UserState;

/**
 * Handle sending emails on different events
 * @package App\Listeners
 */
class NotificationEventListener
{
    public function subscribe($events)
    {
        $events->listen(
            \App\Events\UserRegistered::class,
            'App\Listeners\NotificationEventListener@onUserRegister'
        );

        $events->listen(
            \App\Events\UserStateChanged::class,
            'App\Listeners\NotificationEventListener@onUserStateChange'
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
                  . UserState::label($event->user->state)
                  . ', sending active email');

        # First send the admin a notification
        $admin_email = setting('general.admin_email');
        Log::info('Sending admin notification email to "'.$admin_email.'"');

        if (!empty($admin_email)) {
            $email = new \App\Mail\Admin\UserRegistered($event->user);
            Mail::to($admin_email)->send($email);
        }

        # Then notify the user
        if($event->user->state === UserState::ACTIVE) {
            $email = new \App\Mail\UserRegistered(
                $event->user,
                'Welcome to ' . config('app.name') . '!'
            );

            Mail::to($event->user->email)->send($email);
        } else if($event->user->state === UserState::PENDING) {
            Mail::to($event->user->email)->send(new \App\Mail\UserPending($event->user));
        }
    }

    /**
     * When a user's state changes, send an email out
     * @param UserStateChanged $event
     */
    public function onUserStateChange(UserStateChanged $event)
    {
        if ($event->old_state === UserState::PENDING) {
            if ($event->user->state === UserState::ACTIVE)
            {
                $email = new \App\Mail\UserRegistered(
                    $event->user,
                    'Your registration has been accepted!'
                );

                Mail::to($event->user->email)->send($email);
            }

            else if ($event->user->state === UserState::REJECTED)
            {
                $email = new \App\Mail\UserRejected($event->user);
                Mail::to($event->user->email)->send($email);
            }
        }

        # TODO: Other state transitions
        elseif ($event->old_state === UserState::ACTIVE)
        {

        }
    }
}
