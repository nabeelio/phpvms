<?php

namespace App\Providers;

use App\Cron\Nightly\ApplyExpenses;
use App\Cron\Nightly\PilotLeave;
use App\Cron\Nightly\RecalculateBalances;
use App\Cron\Nightly\RecalculateStats;
use App\Cron\Nightly\SetActiveFlights;
use App\Events\CronHourly;
use App\Events\CronMonthly;
use App\Events\CronNightly;
use App\Events\CronWeekly;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * All of the hooks for the cron jobs
 */
class CronServiceProvider extends ServiceProvider
{
    protected $listen = [
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
            \App\Cron\Monthly\ApplyExpenses::class,
        ],

        CronHourly::class => [
            \App\Cron\Hourly\RemoveExpiredBids::class,
        ],
    ];
}
