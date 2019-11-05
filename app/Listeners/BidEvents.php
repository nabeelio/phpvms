<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepAccepted;
use App\Services\BidService;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Do stuff with bids - like if a PIREP is accepted, then remove the bid
 */
class BidEvents extends Listener
{
    private $bidSvc;

    public function __construct(BidService $bidSvc)
    {
        $this->bidSvc = $bidSvc;
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
     */
    public function onPirepAccept(PirepAccepted $event): void
    {
        $this->bidSvc->removeBidForPirep($event->pirep);
    }
}
