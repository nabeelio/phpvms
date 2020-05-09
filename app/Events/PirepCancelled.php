<?php

namespace App\Events;

use App\Models\Pirep;

class PirepCancelled extends BaseEvent
{
    public $pirep;

    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
