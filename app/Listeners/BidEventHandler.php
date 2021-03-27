<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepFiled;
use App\Services\BidService;

/**
 * Do stuff with bids - like if a PIREP is accepted, then remove the bid
 */
class BidEventHandler extends Listener
{
    public static $callbacks = [
        PirepFiled::class => 'onPirepFiled',
    ];

    private $bidSvc;

    public function __construct(BidService $bidSvc)
    {
        $this->bidSvc = $bidSvc;
    }

    /**
     * When a PIREP is filed, remove any bids
     *
     * @param PirepFiled $event
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function onPirepFiled(PirepFiled $event): void
    {
        $this->bidSvc->removeBidForPirep($event->pirep);
    }
}
