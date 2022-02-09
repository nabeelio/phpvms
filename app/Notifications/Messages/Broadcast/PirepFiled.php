<?php

namespace App\Notifications\Messages\Broadcast;

use App\Contracts\Notification;
use App\Models\Pirep;
use App\Notifications\Channels\Discord\DiscordMessage;
use App\Support\Units\Distance;
use App\Support\Units\Time;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

class PirepFiled extends Notification implements ShouldQueue
{
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
    }

    public function via($notifiable)
    {
        return ['discord_webhook'];
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
        $title = 'Flight '.$pirep->ident.' Filed';
        $fields = [
            'Flight'            => $pirep->ident,
            'Departure Airport' => $pirep->dpt_airport_id,
            'Arrival Airport'   => $pirep->arr_airport_id,
            'Equipment'         => $pirep->aircraft->ident,
            'Flight Time'       => Time::minutesToTimeString($pirep->flight_time),
        ];

        if ($pirep->distance) {
            try {
                $distance = new Distance(
                    $pirep->distance,
                    config('phpvms.internal_units.distance')
                );

                $pd = $distance[$distance->unit].' '.$distance->unit;
                $fields['Distance'] = $pd;
            } catch (NonNumericValue|NonStringUnitName $e) {
            }
        }

        $dm = new DiscordMessage();
        return $dm->url(setting('notifications.discord_public_webhook_url'))
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
