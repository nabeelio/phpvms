<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $channels = [];

    public function __construct()
    {
        // Look in the notifications.channels config and see where this particular
        // notification can go. Map it to $channels
        $klass = get_class($this);
        $notif_config = config('notifications.channels', []);
        if (!array_key_exists($klass, $notif_config)) {
            Log::error('Notification type '.$klass.' missing from notifications config');
            return;
        }

        $this->channels = $notif_config[$klass];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return $this->channels;
    }
}
