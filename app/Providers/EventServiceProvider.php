<?php

namespace App\Providers;

use App\Events\Expenses;
use App\Events\PirepFiled;
use App\Events\UserStatsChanged;
use App\Listeners\AwardListener;
use App\Listeners\BidEvents;
use App\Listeners\ExpenseListener;
use App\Listeners\FinanceEvents;
use App\Listeners\SetUserActive;
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
            SetUserActive::class,
        ],

        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UserStatsChanged::class => [
            AwardListener::class,
        ],
    ];

    protected $subscribe = [
        BidEvents::class,
        FinanceEvents::class,
        EventHandler::class,
    ];
}
