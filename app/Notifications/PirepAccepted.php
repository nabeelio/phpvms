<?php

namespace App\Notifications;

use App\Models\Pirep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PirepAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    private $pirep;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Pirep $pirep
     */
    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->from(config('mail.from.address', 'no-reply@phpvms.net'))
            ->subject('PIREP Accepted!')
            ->markdown('mail.pirep.accepted', ['pirep' => $this->pirep]);
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
