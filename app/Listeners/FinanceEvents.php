<?php

namespace App\Listeners;

use App\Events\PirepAccepted;
use App\Events\PirepRejected;
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

        $events->listen(
            PirepRejected::class,
            'App\Listeners\FinanceEvents@onPirepReject'
        );
    }

    /**
     * Kick off the finance events when a PIREP is accepted
     * @param PirepAccepted $event
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function onPirepAccept(PirepAccepted $event)
    {
        $this->financeSvc->processFinancesForPirep($event->pirep);
    }

    /**
     * Delete all finances in the journal for a given PIREP
     * @param PirepRejected $event
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function onPirepReject(PirepRejected $event)
    {
        $this->financeSvc->deleteFinancesForPirep($event->pirep);
    }
}
