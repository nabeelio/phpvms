<?php

namespace App\Listeners;

use App\Contracts\Listener;
use App\Events\PirepPrefiled;

/**
 * Look for and run any of the award classes. Don't modify this.
 * See the documentation on creating awards:
 *
 * @url http://docs.phpvms.net/customizing/awards
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
