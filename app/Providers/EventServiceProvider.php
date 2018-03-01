<?php

namespace App\Providers;

use App\Listeners\FinanceEvents;
use App\Listeners\NotificationEvents;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\ExpenseListener;
use App\Events\Expenses;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Expenses::class => [
            ExpenseListener::class
        ],
    ];

    protected $subscribe = [
        FinanceEvents::class,
        NotificationEvents::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
