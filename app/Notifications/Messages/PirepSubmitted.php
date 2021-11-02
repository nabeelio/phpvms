<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\Pirep;
use App\Notifications\Channels\Discord\DiscordMessage;
use App\Notifications\Channels\Discord\DiscordWebhook;
use App\Notifications\Channels\MailChannel;
use App\Support\Units\Distance;
use App\Support\Units\Time;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

class PirepSubmitted extends Notification implements ShouldQueue
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
            'New PIREP Submitted',
            'notifications.mail.admin.pirep.submitted',
            ['pirep' => $this->pirep]
        );
    }

    public function via($notifiable)
    {
        return ['mail', DiscordWebhook::class];
    }

    /**
     * Send a Discord notification
     *
     * @param Pirep $pirep
     *
     * @return DiscordMessage|null
     */
    public function toDiscordChannel($pirep): ?DiscordMessage
    {
        if (empty(setting('notifications.discord_public_webhook_url'))) {
            return null;
        }

        $title = 'Flight '.$pirep->airline->code.$pirep->ident.' Filed';
        $fields = [
            'Flight'                => $pirep->airline->code.$pirep->ident,
            'Departure Airport'     => $pirep->dpt_airport_id,
            'Arrival Airport'       => $pirep->arr_airport_id,
            'Equipment'             => $pirep->aircraft->ident,
            'Flight Time (Planned)' => Time::minutesToTimeString($pirep->planned_flight_time),
        ];

        if ($pirep->planned_distance) {
            try {
                $planned_distance = new Distance(
                    $pirep->planned_distance,
                    config('phpvms.internal_units.distance')
                );

                $pd = $planned_distance[$planned_distance->unit].' '.$planned_distance->unit;
                $fields['Distance (Planned)'] = $pd;
            } catch (NonNumericValue|NonStringUnitName $e) {
            }
        }

        $dm = new DiscordMessage();
        return $dm->webhook(setting('notifications.discord_public_webhook_url'))
            ->success()
            ->title($title)
            ->description($pirep->user->discord_id ? 'Flight by <@'.$pirep->user->discord_id.'>' : '')
            ->url(route('frontend.pireps.show', [$pirep->id]))
            ->author([
                'name'     => $pirep->user->ident.' - '.$pirep->user->name_private,
                'url'      => route('frontend.profile.show', [$pirep->user_id]),
                'icon_url' => $pirep->user->resolveAvatarUrl(),
            ])
            ->fields($fields);
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
