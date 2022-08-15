<?php

namespace App\Events;

use App\Contracts\Event;
use App\Models\Pirep;

class PirepAccepted extends Event
{
    public Pirep $pirep;

    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
