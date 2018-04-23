<?php

namespace App\Events;

use App\Models\Pirep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PirepFiled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $pirep;

    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
