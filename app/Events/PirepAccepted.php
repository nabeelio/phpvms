<?php

namespace App\Events;

use App\Models\Pirep;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PirepAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pirep;

    /**
     * Create a new event instance.
     */
    public function __construct(Pirep $pirep)
    {
        //
    }
}
