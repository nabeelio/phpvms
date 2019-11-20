<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepAccepted;
use App\Events\PirepRejected;
use App\Services\BidService;

/**
 * Do stuff with bids - like if a PIREP is accepted, then remove the bid
 */
class BidEventHandler extends Listener
{
    public static $callbacks = [
        PirepAccepted::class => 'onPirepAccept',
        PirepRejected::class => 'onPirepReject',
    ];

    private $bidSvc;

    public function __construct(BidService $bidSvc)
    {
        $this->bidSvc = $bidSvc;
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

    /**
     * When a PIREP is accepted, remove any bids
     *
     * @param PirepRejected $event
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function onPirepReject(PirepRejected $event): void
    {
        $this->bidSvc->removeBidForPirep($event->pirep);
    }
}
