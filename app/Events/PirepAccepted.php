<?php

namespace App\Events;

use App\Models\Pirep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PirepAccepted
 */
class PirepAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pirep;

    /**
     * PirepAccepted constructor.
     *
     * @param Pirep $pirep
     */
    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
