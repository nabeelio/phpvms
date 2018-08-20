<?php

namespace App\Providers;

use App\Cron\Nightly\RecalculateStats;
use App\Cron\Nightly\SetActiveFlights;
use App\Events\CronMonthly;
use App\Events\CronNightly;
use App\Events\CronWeekly;
use App\Cron\Nightly\ApplyExpenses;
use App\Cron\Nightly\PilotLeave;
use App\Cron\Nightly\RecalculateBalances;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class CronServiceProvider extends ServiceProvider
{
    protected $listen = [
        # Cron hooks
        CronNightly::class => [
            ApplyExpenses::class,
            RecalculateBalances::class,
            PilotLeave::class,
            SetActiveFlights::class,
            RecalculateStats::class,
        ],

        CronWeekly::class => [
        ],

        CronMonthly::class => [
            \App\Cron\Monthly\ApplyExpenses::class
        ],
    ];

    protected $subscribe = [];
}
