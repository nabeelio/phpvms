<?php

namespace App\Notifications;

use App\Contracts\Listener;
use App\Events\NewsAdded;
use App\Events\PirepAccepted;
use App\Events\PirepFiled;
use App\Events\PirepRejected;
use App\Events\UserRegistered;
use App\Events\UserStateChanged;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Notifications\Messages\PirepSubmitted;
use App\Notifications\Messages\UserPending;
use App\Notifications\Messages\UserRejected;
use App\Notifications\Notifiables\Broadcast;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Listen for different events and map them to different notifications
 */
class EventHandler extends Listener
{
    private static $broadcastNotifyable;

    public static $callbacks = [
        NewsAdded::class        => 'onNewsAdded',
        PirepAccepted::class    => 'onPirepAccepted',
        PirepFiled::class       => 'onPirepFile',
        PirepRejected::class    => 'onPirepRejected',
        UserRegistered::class   => 'onUserRegister',
        UserStateChanged::class => 'onUserStateChange',
    ];

    public function __construct()
    {
        static::$broadcastNotifyable = app(Broadcast::class);
    }

    /**
     * Send a notification to all of the admins
     *
     * @param \App\Contracts\Notification $notification
     */
    protected function notifyAdmins($notification)
    {
        $admin_users = User::whereRoleIs('admin')->get();

        try {
            Notification::send($admin_users, $notification);
        } catch (Exception $e) {
            Log::emergency('Error emailing admins, malformed email='.$e->getMessage());
        }
    }

    /**
     * @param User                        $user
     * @param \App\Contracts\Notification $notification
     */
    protected function notifyUser($user, $notification)
    {
        try {
            $user->notify($notification);
        } catch (Exception $e) {
            Log::emergency('Error emailing admins, malformed email='.$e->getMessage());
        }
    }

    /**
     * Send a notification to all users. Also can specify if a particular notification
     * requires an opt-in
     *
     * @param \App\Contracts\Notification $notification
     */
    protected function notifyAllUsers(\App\Contracts\Notification $notification)
    {
        $where = [];
        if ($notification->requires_opt_in === true) {  // If the opt-in is required
            $where['opt_in'] = true;
        }

        /** @var Collection $users */
        $users = User::where($where)->get();
        if (empty($users) || $users->count() === 0) {
            return;
        }

        Log::info('Sending notification to '.$users->count().' users');

        try {
            Notification::send($users, $notification);
        } catch (Exception $e) {
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
            .UserState::label($event->user->state).', sending active email');

        /*
         * Send all of the admins a notification that a new user registered
         */
        $this->notifyAdmins(new Messages\AdminUserRegistered($event->user));

        /*
         * Send the user a confirmation email
         */
        if ($event->user->state === UserState::ACTIVE) {
            $this->notifyUser($event->user, new Messages\UserRegistered($event->user));
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
                $this->notifyUser($event->user, new Messages\UserRegistered($event->user));
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
        $this->notifyUser($event->pirep->user, new Messages\PirepAccepted($event->pirep));
    }

    /**
     * Notify the user that their PIREP has been accepted
     *
     * @param \App\Events\PirepRejected $event
     */
    public function onPirepRejected(PirepRejected $event): void
    {
        Log::info('NotificationEvents::onPirepRejected: '.$event->pirep->id.' rejected');
        $this->notifyUser($event->pirep->user, new Messages\PirepRejected($event->pirep));
    }

    /**
     * Notify all users of a news event, but only the users which have opted in
     *
     * @param \App\Events\NewsAdded $event
     */
    public function onNewsAdded(NewsAdded $event): void
    {
        Log::info('NotificationEvents::onNewsAdded');
        $this->notifyAllUsers(new Messages\NewsAdded($event->news));
    }
}
