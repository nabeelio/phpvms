<?php

namespace Modules\Vacentral\Providers;

use App\Events\PirepAccepted;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Vacentral\Listeners\PirepAcceptedEventListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        PirepAccepted::class => [PirepAcceptedEventListener::class],
    ];
}
