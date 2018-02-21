<?php

namespace App\Events;

use App\Models\Pirep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
