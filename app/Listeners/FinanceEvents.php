<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepAccepted;
use App\Events\PirepRejected;
use App\Services\Finance\PirepFinanceService;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Subscribe for events that we do some financial processing for
 * This includes when a PIREP is accepted, or rejected
 */
class FinanceEvents extends Listener
{
    private $financeSvc;

    /**
     * FinanceEvents constructor.
     *
     * @param PirepFinanceService $financeSvc
     */
    public function __construct(
        PirepFinanceService $financeSvc
    ) {
        $this->financeSvc = $financeSvc;
    }

    /**
     * @param $events
     */
    public function subscribe(Dispatcher $events): void
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
     *
     * @param PirepAccepted $event
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function onPirepAccept(PirepAccepted $event): void
    {
        $this->financeSvc->processFinancesForPirep($event->pirep);
    }

    /**
     * Delete all finances in the journal for a given PIREP
     *
     * @param PirepRejected $event
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function onPirepReject(PirepRejected $event): void
    {
        $this->financeSvc->deleteFinancesForPirep($event->pirep);
    }
}
