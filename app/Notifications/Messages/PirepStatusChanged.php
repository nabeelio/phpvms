<?php

namespace App\Notifications\Messages;

use App\Contracts\Notification;
use App\Models\Enums\PirepStatus;
use App\Models\Pirep;
use App\Notifications\Channels\Discord\DiscordMessage;
use App\Notifications\Channels\Discord\DiscordWebhook;
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

    // TODO: Int'l languages for these
    protected static $verbs = [
        PirepStatus::INITIATED     => 'is initialized',
        PirepStatus::SCHEDULED     => 'is scheduled',
        PirepStatus::BOARDING      => 'is boarding',
        PirepStatus::RDY_START     => 'is ready for start',
        PirepStatus::PUSHBACK_TOW  => 'is pushing back',
        PirepStatus::DEPARTED      => 'has departed',
        PirepStatus::RDY_DEICE     => 'is ready for de-icing',
        PirepStatus::STRT_DEICE    => 'is de-icing',
        PirepStatus::GRND_RTRN     => 'on ground return',
        PirepStatus::TAXI          => 'is taxiing',
        PirepStatus::TAKEOFF       => 'has taken off',
        PirepStatus::INIT_CLIM     => 'in initial climb',
        PirepStatus::AIRBORNE      => 'is enroute',
        PirepStatus::ENROUTE       => 'is enroute',
        PirepStatus::DIVERTED      => 'has diverted',
        PirepStatus::APPROACH      => 'on approach',
        PirepStatus::APPROACH_ICAO => 'on approach',
        PirepStatus::ON_FINAL      => 'on final approach',
        PirepStatus::LANDING       => 'is landing',
        PirepStatus::LANDED        => 'has landed',
        PirepStatus::ARRIVED       => 'has arrived',
        PirepStatus::CANCELLED     => 'is cancelled',
        PirepStatus::PAUSED        => 'is paused',
        PirepStatus::EMERG_DESCENT => 'in emergency descent',
    ];

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
        return [DiscordWebhook::class];
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

        $title = 'Flight '.$pirep->airline->code.$pirep->ident.' '.self::$verbs[$pirep->status];

        $fields = [
            'Flight'            => $pirep->airline->code.$pirep->ident,
            'Departure Airport' => $pirep->dpt_airport_id,
            'Arrival Airport'   => $pirep->arr_airport_id,
            'Equipment'         => $pirep->aircraft->ident,
            'Flight Time'       => Time::minutesToTimeString($pirep->flight_time),
        ];

        // Show the distance, but include the planned distance if it's been set
        if ($pirep->distance) {
            $unit = config('phpvms.internal_units.distance');

            try {
                $planned_distance = new Distance($pirep->distance, $unit);
                $pd = $planned_distance[$planned_distance->unit];
                $fields['Distance'] = $pd;

                // Add the planned distance in
                if ($pirep->planned_distance) {
                    try {
                        $planned_distance = new Distance($pirep->planned_distance, $unit);
                        $pd = $planned_distance[$planned_distance->unit];
                        $fields['Distance'] .= '/'.$pd;
                    } catch (NonNumericValue|NonStringUnitName $e) {
                    }
                }

                $fields['Distance'] .= ' '.$planned_distance->unit;
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
