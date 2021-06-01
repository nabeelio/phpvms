<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\User;
use App\Notifications\Channels\MailChannel;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistered extends Notification implements ShouldQueue
{
    use MailChannel;

    private $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user
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

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
        ];
    }
}
