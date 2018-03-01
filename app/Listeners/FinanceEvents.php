<?php

namespace App\Listeners;

use \App\Events\PirepAccepted;
use App\Services\FinanceService;

/**
 * Subscribe for events that we do some financial processing for
 * This includes when a PIREP is accepted, or rejected
 * @package App\Listeners
 */
class FinanceEvents
{
    private $financeSvc;

    public function __construct(
        FinanceService $financeSvc
    ) {
        $this->financeSvc = $financeSvc;
    }

    public function subscribe($events)
    {
        $events->listen(
            PirepAccepted::class,
            'App\Listeners\FinanceEvents@onPirepAccept'
        );
    }

    /**
     * Kick off the finance events when a PIREP is accepted
     * @param PirepAccepted $event
     */
    public function onPirepAccept(PirepAccepted $event)
    {
        $this->financeSvc->processFinancesForPirep($event->pirep);
    }
}
