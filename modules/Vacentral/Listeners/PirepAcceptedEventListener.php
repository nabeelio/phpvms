<?php

namespace Modules\Vacentral\Listeners;

use App\Events\PirepAccepted;
use Log;

class PirepAcceptedEventListener
{
    /**
     * Handle the event.
     */
    public function handle(PirepAccepted $pirep)
    {
        Log::info('Received PIREP accepted event', [$pirep]);
    }
}
