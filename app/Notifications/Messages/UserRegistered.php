<?php

namespace App\Notifications\Messages;

use App\Models\User;
use App\Notifications\BaseNotification;
use App\Notifications\Channels\MailChannel;

class UserRegistered extends BaseNotification
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
            'Welcome to '.config('app.name').'!',
            'notifications.mail.user.registered',
            ['user' => $this->user]
        );
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
        ];
    }
}
