<?php

namespace App\Providers;

use App\Events\Expenses;
use App\Events\UserStatsChanged;
use App\Listeners\AwardListener;
use App\Listeners\ExpenseListener;
use App\Listeners\FinanceEvents;
use App\Listeners\NotificationEvents;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Expenses::class => [
            ExpenseListener::class,
        ],

        UserStatsChanged::class => [
            AwardListener::class,
        ],
    ];

    protected $subscribe = [
        FinanceEvents::class,
        NotificationEvents::class,
    ];
}
