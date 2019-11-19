<?php

namespace App\Notifications;

use App\Models\Pirep;
use App\Notifications\Channels\MailChannel;

class PirepRejected extends BaseNotification
{
    use MailChannel;

    public $channels = ['mail'];

    private $pirep;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Pirep $pirep
     */
    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;

        $this->setMailable(
            'PIREP Rejected!',
            'notifications.mail.pirep.rejected',
            ['pirep' => $this->pirep]
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
            'pirep_id' => $this->pirep->id,
            'user_id'  => $this->pirep->user_id,
        ];
    }
}
