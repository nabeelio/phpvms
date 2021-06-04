<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\Pirep;
use App\Notifications\Channels\MailChannel;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Send the PIREP accepted message to a particular user, can also be sent to Discord
 */
class PirepAccepted extends Notification implements ShouldQueue
{
    use MailChannel;

    private $pirep;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Pirep $pirep
     */
    public function __construct(Pirep $pirep)
    {
        parent::__construct();

        $this->pirep = $pirep;

        $this->setMailable(
            'PIREP Accepted!',
            'notifications.mail.pirep.accepted',
            ['pirep' => $this->pirep]
        );
    }

    public function via($notifiable)
    {
        return ['mail'];
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
