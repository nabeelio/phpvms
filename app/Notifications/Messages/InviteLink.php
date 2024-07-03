<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\Invite;
use App\Notifications\Channels\MailChannel;

class InviteLink extends Notification
{
    use MailChannel;

    /**
     * Create a new notification instance.
     *
     * @param Invite $invite
     */
    public function __construct(
        private readonly Invite $invite
    ) {
        parent::__construct();

        $this->setMailable(
            'You have been invited to join '.config('app.name'),
            'notifications.mail.user.invite',
            ['invite' => $invite]
        );
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param $notifiable
     *
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'invite_id' => $this->invite->id,
        ];
    }
}
