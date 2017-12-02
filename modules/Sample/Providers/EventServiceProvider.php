<?php

namespace Modules\Sample\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        'App\Events\TestEvent' => [
            'Modules\Sample\Listeners\TestEventListener',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
