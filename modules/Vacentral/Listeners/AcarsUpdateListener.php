<?php

namespace Modules\Vacentral\Listeners;

use App\Events\AcarsUpdate;
use Illuminate\Support\Facades\Log;
use VaCentral\Contracts\IVaCentral;

class AcarsUpdateListener
{
    private $vacentral;

    public function __construct(IVaCentral $vacentral)
    {
        $this->vacentral = $vacentral;
    }

    /**
     * @param AcarsUpdate $event
     */
    public function handle(AcarsUpdate $event)
    {
        Log::info('Received acars update event', [$event]);
    }
}
