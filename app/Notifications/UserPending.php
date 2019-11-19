<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Channels\MailChannel;

class UserPending extends BaseNotification
{
    use MailChannel;

    public $channels = ['mail'];

    private $user;

    /**
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        $this->setMailable(
            'Your registration is pending',
            'notifications.mail.user.pending',
            ['user' => $this->user]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
        ];
    }
}
