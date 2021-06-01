<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\Enums\PirepStatus;
use App\Models\Pirep;
use App\Notifications\Channels\Discord\Discord;
use App\Notifications\Channels\Discord\DiscordMessage;
use App\Support\Units\Distance;
use App\Support\Units\Time;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;

/**
 * Send the PIREP accepted message to a particular user, can also be sent to Discord
 */
class PirepStatusChanged extends Notification implements ShouldQueue
{
    private $pirep;

    /**
     * Create a new notification instance.
     *
     * @param Pirep $pirep
     */
    public function __construct(Pirep $pirep)
    {
        parent::__construct();
        $this->pirep = $pirep;
    }

    public function via($notifiable)
    {
        return [Discord::class];
    }

    /**
     * Send a Discord notification
     *
     * @param Pirep $pirep
     *
     * @return DiscordMessage
     */
    public function toDiscordChannel($pirep): DiscordMessage
    {
        $title = 'Flight '.$pirep->airline->code.$pirep->ident.' is now '.PirepStatus::label($pirep->status);
        $fields = [
            'Flight'            => $pirep->airline->code.$pirep->ident,
            'Departure Airport' => $pirep->dpt_airport_id,
            'Arrival Airport'   => $pirep->arr_airport_id,
            'Equipment'         => $pirep->aircraft->ident,
            'Flight Time'       => Time::minutesToTimeString($pirep->flight_time),
        ];

        if ($pirep->distance) {
            try {
                $planned_distance = new Distance(
                    $pirep->distance,
                    config('phpvms.internal_units.distance')
                );

                $pd = $planned_distance[$planned_distance->unit];
                $fields['Distance'] = $pd;

                // Add the planned distance in
                if ($pirep->planned_distance) {
                    try {
                        $planned_distance = new Distance(
                            $pirep->planned_distance,
                            config('phpvms.internal_units.distance')
                        );

                        $pd = $planned_distance[$planned_distance->unit];
                        $fields['Distance'] .= '/'.$pd;
                    } catch (NonNumericValue $e) {
                    } catch (NonStringUnitName $e) {
                    }
                }

                $fields['Distance'] .= ' '.$planned_distance->unit;
            } catch (NonNumericValue $e) {
            } catch (NonStringUnitName $e) {
            }
        }

        $dm = new DiscordMessage();
        return $dm->webhook(setting('notifications.discord_public_webhook_url'))
            ->success()
            ->title($title)
            ->url(route('frontend.pireps.show', [$pirep->id]))
            ->author([
                'name'     => $pirep->user->ident.' - '.$pirep->user->name_private,
                'url'      => route('frontend.pireps.show', [$pirep->id]),
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
