<?php

namespace App\Events;

use App\Models\Pirep;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PirepRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pirep;

    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
