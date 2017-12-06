<?php

namespace Modules\Vacentral\Providers;

use App\Events\TestEvent;
use Modules\Vacentral\Listeners\TestEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        TestEvent::class => [TestEventListener::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
