<?php

namespace App\Notifications\Notifiables;

use Illuminate\Notifications\Notifiable;

/**
 * These are notifications that get broadcasted, not to a single person
 * (e.g, on Discord, Slack or Telegram or something)
 *
 * $notifyable = app(Broadcast::class);
 * $notifyable->notify($eventclass);
 *
 * @package App\Notifications\Notifiables
 */
class Broadcast
{
    use Notifiable;
}
