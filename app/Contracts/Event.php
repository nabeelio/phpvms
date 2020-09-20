<?php

namespace App\Contracts;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Event
{
    use Dispatchable;
    use SerializesModels;
}
