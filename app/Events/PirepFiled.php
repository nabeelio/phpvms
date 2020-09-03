<?php

namespace App\Events;

use App\Contracts\Event;
use App\Models\Pirep;

class PirepFiled extends Event
{
    public $pirep;

    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
