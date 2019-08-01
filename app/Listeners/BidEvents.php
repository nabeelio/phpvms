<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepAccepted;
use App\Services\PirepService;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Do stuff with bids - like if a PIREP is accepted, then remove the bid
 */
class BidEvents extends Listener
{
    private $pirepSvc;

    /**
     * FinanceEvents constructor.
     *
     * @param PirepService $pirepSvc
     */
    public function __construct(
        PirepService $pirepSvc
    ) {
        $this->pirepSvc = $pirepSvc;
    }

    /**
     * @param $events
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            PirepAccepted::class,
            'App\Listeners\BidEvents@onPirepAccept'
        );
    }

    /**
     * When a PIREP is accepted, remove any bids
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
        $this->pirepSvc->removeBid($event->pirep);
    }
}
