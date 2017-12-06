<?php

namespace Modules\Vacentral\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PirepAccepted;
use Modules\Vacentral\Listeners\PirepAcceptedEventListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        PirepAccepted::class => [PirepAcceptedEventListener::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
