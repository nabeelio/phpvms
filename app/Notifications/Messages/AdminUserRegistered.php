<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\User;
use App\Notifications\Channels\MailChannel;

class AdminUserRegistered extends Notification
{
    use MailChannel;

    public $channels = ['mail'];

    private $user;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        parent::__construct();

        $this->user = $user;
        $this->setMailable(
            'A new user registered',
            'notifications.mail.admin.user.registered',
            ['user' => $user]
        );
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
        ];
    }
}
