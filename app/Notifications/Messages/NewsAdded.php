<?php

namespace App\Notifications\Messages;

use App\Models\News;
use App\Notifications\BaseNotification;
use App\Notifications\Channels\MailChannel;

class NewsAdded extends BaseNotification
{
    use MailChannel;

    public $channels = ['mail'];

    private $news;

    public function __construct(News $news)
    {
        parent::__construct();

        $this->news = $news;
        $this->setMailable(
            $news->subject,
            'notifications.mail.news',
            ['news' => $news]
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
            'news_id' => $this->news->id,
        ];
    }
}
