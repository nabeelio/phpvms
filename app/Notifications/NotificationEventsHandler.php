<?php

namespace App\Notifications;

use App\Contracts\Listener;
use App\Events\NewsAdded;
use App\Events\PirepAccepted;
use App\Events\PirepFiled;
use App\Events\PirepPrefiled;
use App\Events\PirepRejected;
use App\Events\PirepStatusChange;
use App\Events\UserRegistered;
use App\Events\UserStateChanged;
use App\Models\Enums\PirepStatus;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Notifications\Messages\UserRejected;
use App\Notifications\Notifiables\Broadcast;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Listen for different events and map them to different notifications
 */
class NotificationEventsHandler extends Listener
{
    private static $broadcastNotifyable;

    public static $callbacks = [
        NewsAdded::class         => 'onNewsAdded',
        PirepPrefiled::class     => 'onPirepPrefile',
        PirepStatusChange::class => 'onPirepStatusChange',
        PirepAccepted::class     => 'onPirepAccepted',
        PirepFiled::class        => 'onPirepFile',
        PirepRejected::class     => 'onPirepRejected',
        UserRegistered::class    => 'onUserRegister',
        UserStateChanged::class  => 'onUserStateChange',
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
    protected function notifyAdmins(\App\Contracts\Notification $notification)
    {
        $admin_users = User::whereRoleIs('admin')->get();

        foreach ($admin_users as $user) {
            if (empty($user->email)) {
                continue;
            }

            try {
                // $this->notifyUser($user, $notification);
                Notification::send([$user], $notification);
            } catch (Exception $e) {
                Log::emergency('Error emailing admin ('.$user->email.'). Error='.$e->getMessage());
            }
        }
    }

    /**
     * @param User                        $user
     * @param \App\Contracts\Notification $notification
     */
    protected function notifyUser(User $user, \App\Contracts\Notification $notification)
    {
        if ($user->state === UserState::DELETED) {
            return;
        }

        try {
            $user->notify($notification);
        } catch (Exception $e) {
            Log::emergency('Error emailing user, '.$user->ident.'='.$user->email.', error='.$e->getMessage());
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
        $users = User::where($where)->where('state', '<>', UserState::DELETED)->get();
        if (empty($users) || $users->count() === 0) {
            return;
        }

        Log::info('Sending notification to '.$users->count().' users');

        foreach ($users as $user) {
            $this->notifyUser($user, $notification);
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
         * Send the user a confirmation email
         */
        if ($event->user->state === UserState::ACTIVE) {
            $this->notifyUser($event->user, new Messages\UserRegistered($event->user));
        } elseif ($event->user->state === UserState::PENDING) {
            $this->notifyUser($event->user, new Messages\UserPending($event->user));
        }

        /*
         * Send all of the admins a notification that a new user registered
         */
        $this->notifyAdmins(new Messages\AdminUserRegistered($event->user));

        /*
         * Broadcast notifications
         */
        Notification::send([$event->user], new Messages\Broadcast\UserRegistered($event->user));
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
     * Prefile notification. Disabled intentionally, No need to send it to Discord
     */
    public function onPirepPrefile(PirepPrefiled $event): void
    {
        Log::info('NotificationEvents::onPirepPrefile: '.$event->pirep->id.' prefiled');

        /*
         * Broadcast notifications
         */
        // Notification::send([$event->pirep], new Messages\Broadcast\PirepPrefiled($event->pirep));
    }

    /**
     * Status Change notification.
     * Reduced the messages (Boarding, Pushback, TakeOff, Landing and non-normals only)
     * If needed array can be tied to a setting at admin side for further customization
     */
    public function onPirepStatusChange(PirepStatusChange $event): void
    {
        Log::info('NotificationEvents::onPirepStatusChange: '.$event->pirep->id.' status changed');

        $message_types = [
            PirepStatus::BOARDING,
            PirepStatus::PUSHBACK_TOW,
            PirepStatus::GRND_RTRN,
            PirepStatus::TAKEOFF,
            PirepStatus::LANDED,
            PirepStatus::DIVERTED,
            PirepStatus::CANCELLED,
            PirepStatus::PAUSED,
            PirepStatus::EMERG_DESCENT,
        ];

        if (setting('notifications.discord_pirep_status', true) && in_array($event->pirep->status, $message_types, true)) {
            Notification::send([$event->pirep], new Messages\Broadcast\PirepStatusChanged($event->pirep));
        }
    }

    /**
     * Notify the admins that a new PIREP has been filed
     *
     * @param PirepFiled $event
     */
    public function onPirepFile(PirepFiled $event): void
    {
        Log::info('NotificationEvents::onPirepFile: '.$event->pirep->id.' filed');
        if (setting('notifications.mail_pirep_admin', true)) {
            $this->notifyAdmins(new Messages\PirepFiled($event->pirep));
        }

        /*
         * Broadcast notifications
         */
        Notification::send([$event->pirep], new Messages\Broadcast\PirepFiled($event->pirep));
    }

    /**
     * Notify the user that their PIREP has been accepted
     *
     * @param \App\Events\PirepAccepted $event
     */
    public function onPirepAccepted(PirepAccepted $event): void
    {
        if (setting('notifications.mail_pirep_user_ack', true)) {
            Log::info('NotificationEvents::onPirepAccepted: '.$event->pirep->id.' accepted');
            $this->notifyUser($event->pirep->user, new Messages\PirepAccepted($event->pirep));
        }
    }

    /**
     * Notify the user that their PIREP has been accepted
     *
     * @param \App\Events\PirepRejected $event
     */
    public function onPirepRejected(PirepRejected $event): void
    {
        if (setting('notifications.mail_pirep_user_rej', true)) {
            Log::info('NotificationEvents::onPirepRejected: '.$event->pirep->id.' rejected');
            $this->notifyUser($event->pirep->user, new Messages\PirepRejected($event->pirep));
        }
    }

    /**
     * Notify all users of a news event, but only the users which have opted in
     *
     * @param \App\Events\NewsAdded $event
     */
    public function onNewsAdded(NewsAdded $event): void
    {
        Log::info('NotificationEvents::onNewsAdded');
        if (setting('notifications.mail_news', true)) {
            $this->notifyAllUsers(new Messages\NewsAdded($event->news));
        }

        /*
         * Broadcast notifications
         */
        Notification::send([$event->news], new Messages\Broadcast\NewsAdded($event->news));
    }
}
