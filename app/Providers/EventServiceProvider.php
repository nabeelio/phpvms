<?php

namespace App\Providers;

use App\Cron\Nightly\SetActiveFlights;
use App\Events\CronMonthly;
use App\Events\CronNightly;
use App\Events\CronWeekly;
use App\Events\Expenses;
use App\Events\UserStatsChanged;
use App\Listeners\AwardListener;
use App\Cron\Nightly\ApplyExpenses;
use App\Cron\Nightly\PilotLeave;
use App\Cron\Nightly\RecalculateBalances;
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
            ApplyExpenses::class,
            RecalculateBalances::class,
            PilotLeave::class,
            SetActiveFlights::class,
        ],

        CronWeekly::class => [
        ],

        CronMonthly::class => [
            \App\Cron\Monthly\ApplyExpenses::class
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
