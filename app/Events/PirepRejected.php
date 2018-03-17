<?php

namespace App\Events;

use App\Models\Pirep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PirepRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pirep;

    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
