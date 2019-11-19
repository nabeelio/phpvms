<?php

namespace App\Notifications;

use App\Contracts\Listener;
use App\Events\PirepAccepted;
use App\Events\PirepFiled;
use App\Events\PirepRejected;
use App\Events\UserRegistered;
use App\Events\UserStateChanged;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Notifications\Events\PirepSubmitted;
use App\Notifications\Events\UserPending;
use App\Notifications\Events\UserRejected;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Handle sending emails on different events
 */
class EventHandler extends Listener
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(UserRegistered::class, 'App\Notifications\EventHandler@onUserRegister');
        $events->listen(UserStateChanged::class, 'App\Notifications\EventHandler@onUserStateChange');
        $events->listen(PirepFiled::class, 'App\Notifications\EventHandler@onPirepFile');
        $events->listen(PirepAccepted::class, 'App\Notifications\EventHandler@onPirepAccepted');
        $events->listen(PirepRejected::class, 'App\Notifications\EventHandler@onPirepRejected');
    }

    /**
     * Send a notification to all of the admins
     *
     * @param \Illuminate\Notifications\Notification $notification
     */
    protected function notifyAdmins($notification)
    {
        $admin_users = User::whereRoleIs('admin')->get();

        try {
            Notification::send($admin_users, $notification);
        } catch (\Exception $e) {
            Log::emergency('Error emailing admins, malformed email='.$e->getMessage());
        }
    }

    /**
     * @param User                                   $user
     * @param \Illuminate\Notifications\Notification $notification
     */
    protected function notifyUser($user, $notification)
    {
        try {
            $user->notify($notification);
        } catch (\Exception $e) {
            Log::emergency('Error emailing admins, malformed email='.$e->getMessage());
        }
    }

    /**
     * Send an email when the user registered
     *
     * @param UserRegistered $event
     */
    public function onUserRegister(UserRegistered $event): void
    {
        Log::info('NotificationEvents::onUserRegister: '
            .$event->user->ident.' is '
            .UserState::label($event->user->state)
            .', sending active email');

        /*
         * Send all of the admins a notification that a new user registered
         */
        $this->notifyAdmins(new Events\Admin\UserRegistered($event->user));

        /*
         * Send the user a confirmation email
         */
        if ($event->user->state === UserState::ACTIVE) {
            $this->notifyUser($event->user, new Events\UserRegistered($event->user));
        } elseif ($event->user->state === UserState::PENDING) {
            $this->notifyUser($event->user, new UserPending($event->user));
        }
    }

    /**
     * When a user's state changes, send an email out
     *
     * @param UserStateChanged $event
     */
    public function onUserStateChange(UserStateChanged $event): void
    {
        Log::info('NotificationEvents::onUserStateChange: New user state='.$event->user->state);

        if ($event->old_state === UserState::PENDING) {
            if ($event->user->state === UserState::ACTIVE) {
                $this->notifyUser($event->user, new Events\UserRegistered($event->user));
            } elseif ($event->user->state === UserState::REJECTED) {
                $this->notifyUser($event->user, new UserRejected($event->user));
            }
        } elseif ($event->old_state === UserState::ACTIVE) {
            Log::info('User state change from active to ??');
        }
    }

    /**
     * Notify the admins that a new PIREP has been filed
     *
     * @param \App\Events\PirepFiled $event
     */
    public function onPirepFile(PirepFiled $event): void
    {
        Log::info('NotificationEvents::onPirepFile: '.$event->pirep->id.' filed ');
        $this->notifyAdmins(new PirepSubmitted($event->pirep));
    }

    /**
     * Notify the user that their PIREP has been accepted
     *
     * @param \App\Events\PirepAccepted $event
     */
    public function onPirepAccepted(PirepAccepted $event): void
    {
        Log::info('NotificationEvents::onPirepAccepted: '.$event->pirep->id.' accepted');
        $this->notifyUser($event->pirep->user, new Events\PirepAccepted($event->pirep));
    }

    /**
     * Notify the user that their PIREP has been accepted
     *
     * @param \App\Events\PirepRejected $event
     */
    public function onPirepRejected(PirepRejected $event): void
    {
        Log::info('NotificationEvents::onPirepRejected: '.$event->pirep->id.' rejected');
        $this->notifyUser($event->pirep->user, new Events\PirepRejected($event->pirep));
    }
}
