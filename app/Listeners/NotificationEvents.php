<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Events\UserStateChanged;
use App\Interfaces\Listener;
use App\Models\Enums\UserState;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;
use Log;

/**
 * Handle sending emails on different events
 */
class NotificationEvents extends Listener
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            \App\Events\UserRegistered::class,
            'App\Listeners\NotificationEvents@onUserRegister'
        );

        $events->listen(
            \App\Events\UserStateChanged::class,
            'App\Listeners\NotificationEvents@onUserStateChange'
        );
    }

    /**
     * @return bool
     */
    protected function mailerActive(): bool
    {
        if (empty(config('mail.host'))) {
            Log::info('No mail host specified!');
            return false;
        }

        return true;
    }

    /**
     * @param $to
     * @param $email
     *
     * @return mixed
     */
    protected function sendEmail($to, $email)
    {
        try {
            return Mail::to($to)->send($email);
        } catch (\Exception $e) {
            Log::error('Error sending email!');
            Log::error($e);
        }
    }

    /**
     * Send an email when the user registered
     *
     * @param UserRegistered $event
     */
    public function onUserRegister(UserRegistered $event): void
    {
        Log::info('onUserRegister: '
            .$event->user->pilot_id.' is '
            .UserState::label($event->user->state)
            .', sending active email');

        if (!$this->mailerActive()) {
            return;
        }

        // First send the admin a notification
        $admin_email = setting('general.admin_email');
        Log::info('Sending admin notification email to "'.$admin_email.'"');

        if (!empty($admin_email)) {
            $email = new \App\Mail\Admin\UserRegistered($event->user);
            $this->sendEmail($admin_email, $email);
        }

        // Then notify the user
        if ($event->user->state === UserState::ACTIVE) {
            $email = new \App\Mail\UserRegistered($event->user);
        } elseif ($event->user->state === UserState::PENDING) {
            $email = new \App\Mail\UserPending($event->user);
        }

        $this->sendEmail($event->user->email, $email);
    }

    /**
     * When a user's state changes, send an email out
     *
     * @param UserStateChanged $event
     */
    public function onUserStateChange(UserStateChanged $event): void
    {
        if (!$this->mailerActive()) {
            return;
        }

        if ($event->old_state === UserState::PENDING) {
            if ($event->user->state === UserState::ACTIVE) {
                $email = new \App\Mail\UserRegistered($event->user,
                    'Your registration has been accepted!');
            } elseif ($event->user->state === UserState::REJECTED) {
                $email = new \App\Mail\UserRejected($event->user);
            }
            $this->sendEmail($event->user->email, $email);
        } // TODO: Other state transitions
        elseif ($event->old_state === UserState::ACTIVE) {
            Log::info('User state change from active to ??');
        }
    }
}
