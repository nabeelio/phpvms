<?php

namespace App\Notifications\Messages\Broadcast;

use App\Contracts\Notification;
use App\Models\News;
use App\Notifications\Channels\Discord\DiscordMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewsAdded extends Notification implements ShouldQueue
{
    private $news;

    public function __construct(News $news)
    {
        parent::__construct();

        $this->news = $news;
    }

    public function via($notifiable)
    {
        return ['discord_webhook'];
    }

    /**
     * @param News $news
     *
     * @return DiscordMessage|null
     */
    public function toDiscordChannel($news): ?DiscordMessage
    {
        $dm = new DiscordMessage();
        return $dm->webhook(setting('notifications.discord_public_webhook_url'))
            ->success()
            ->title('News: '.$news->subject)
            ->author([
                'name'     => $news->user->ident.' - '.$news->user->name_private,
                'url'      => '',
                'icon_url' => $news->user->resolveAvatarUrl(),
            ])
            ->description($news->body);
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
            'news_id' => $this->news->id,
        ];
    }
}
