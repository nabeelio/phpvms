<?php

namespace App\Providers;

use App\Events\CronMonthly;
use App\Events\CronNightly;
use App\Events\CronWeekly;
use App\Events\Expenses;
use App\Events\UserStatsChanged;
use App\Listeners\AwardListener;
use App\Listeners\Cron\Nightly\RecalculateBalances;
use App\Listeners\ExpenseListener;
use App\Listeners\FinanceEvents;
use App\Listeners\NotificationEvents;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        Expenses::class    => [
            ExpenseListener::class
        ],

        # Cron hooks
        CronNightly::class => [
            \App\Listeners\Cron\Nightly\ApplyExpenses::class,
            RecalculateBalances::class,
        ],

        CronWeekly::class => [
        ],

        CronMonthly::class => [
            \App\Listeners\Cron\Monthly\ApplyExpenses::class
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
