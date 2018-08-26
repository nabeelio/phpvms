<?php

namespace App\Events;

use App\Models\Pirep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PirepRejected
 */
class PirepRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pirep;

    /**
     * PirepRejected constructor.
     *
     * @param Pirep $pirep
     */
    public function __construct(Pirep $pirep)
    {
        $this->pirep = $pirep;
    }
}
