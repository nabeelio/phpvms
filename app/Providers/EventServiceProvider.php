<?php

namespace App\Providers;

use App\Events\Expenses;
use App\Events\PirepFiled;
use App\Events\UserStatsChanged;
use App\Listeners\AwardListener;
use App\Listeners\BidEventHandler;
use App\Listeners\ExpenseListener;
use App\Listeners\FinanceEventHandler;
use App\Listeners\UserStateListener;
use App\Notifications\EventHandler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Expenses::class => [
            ExpenseListener::class,
        ],

        PirepFiled::class => [
            UserStateListener::class,
        ],

        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UserStatsChanged::class => [
            AwardListener::class,
        ],
    ];

    protected $subscribe = [
        BidEventHandler::class,
        FinanceEventHandler::class,
        EventHandler::class,
    ];
}
