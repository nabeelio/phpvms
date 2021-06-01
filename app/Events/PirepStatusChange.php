<?php

namespace App\Events;

use App\Contracts\Event;
use App\Models\Pirep;

/**
 * Status change like Boarding, Taxi, etc
 */
class PirepStatusChange extends Event
{
    public $pirep;

    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
