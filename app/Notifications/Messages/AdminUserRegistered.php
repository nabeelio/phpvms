<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\User;
use App\Notifications\Channels\Discord\DiscordMessage;
use App\Notifications\Channels\Discord\DiscordWebhook;
use App\Notifications\Channels\MailChannel;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminUserRegistered extends Notification implements ShouldQueue
{
    use MailChannel;

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

    public function via($notifiable)
    {
        return ['mail', DiscordWebhook::class];
    }

    /**
     * Send a Discord notification
     *
     * @param User  $pirep
     * @param mixed $user
     *
     * @return DiscordMessage|null
     */
    public function toDiscordChannel($user): ?DiscordMessage
    {
        if (empty(setting('notifications.discord_private_webhook_url'))) {
            return null;
        }

        $dm = new DiscordMessage();
        return $dm->webhook(setting('notifications.discord_private_webhook_url'))
            ->success()
            ->title('New User Registered: '.$user->ident)
            ->fields([]);
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
        ];
    }
}
