<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepPrefiled;

/**
 * Handler for PIREP events
 */
class PirepEventsHandler extends Listener
{
    /** The events and the callback */
    public static $callbacks = [
        PirepPrefiled::class => 'onPirepPrefile',
    ];

    /**
     * Called when a PIREP is prefiled
     *
     * @param PirepPrefiled $event
     */
    public function onPirepPrefile(PirepPrefiled $event)
    {
    }
}
