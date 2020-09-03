<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\News;
use App\Notifications\Channels\MailChannel;

class NewsAdded extends Notification
{
    use MailChannel;

    public $channels = ['mail'];
    public $requires_opt_in = true;

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
