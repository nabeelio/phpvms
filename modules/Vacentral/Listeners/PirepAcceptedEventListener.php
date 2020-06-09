<?php

namespace Modules\Vacentral\Listeners;

use App\Events\PirepAccepted;
use Illuminate\Support\Facades\Log;

class PirepAcceptedEventListener
{
    /**
     * Handle the event.
     *
     * @param PirepAccepted $pirep
     */
    public function handle(PirepAccepted $pirep)
    {
        Log::info('Received PIREP accepted event', [$pirep]);
    }
}
