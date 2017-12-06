<?php

namespace Modules\Vacentral\Listeners;

use Log;
use App\Events\PirepAccepted;

use Illuminate\Contracts\Queue\ShouldQueue;

class PirepAcceptedEventListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(PirepAccepted $pirep) {
        Log::info('Received event', [$pirep]);
    }
}
