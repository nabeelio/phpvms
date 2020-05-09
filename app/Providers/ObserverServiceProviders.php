<?php

namespace App\Providers;

use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightField;
use App\Models\FlightFieldValue;
use App\Models\Journal;
use App\Models\JournalTransaction;
use App\Models\Observers\AircraftObserver;
use App\Models\Observers\AirportObserver;
use App\Models\Observers\FlightObserver;
use App\Models\Observers\JournalObserver;
use App\Models\Observers\JournalTransactionObserver;
use App\Models\Observers\SettingObserver;
use App\Models\Observers\Sluggable;
use App\Models\Observers\SubfleetObserver;
use App\Models\Observers\UserObserver;
use App\Models\Page;
use App\Models\PirepField;
use App\Models\PirepFieldValue;
use App\Models\Setting;
use App\Models\Subfleet;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProviders extends ServiceProvider
{
    public function boot(): void
    {
        Aircraft::observe(AircraftObserver::class);
        Airport::observe(AirportObserver::class);
        Journal::observe(JournalObserver::class);
        JournalTransaction::observe(JournalTransactionObserver::class);

        Flight::observe(FlightObserver::class);
        FlightField::observe(Sluggable::class);
        FlightFieldValue::observe(Sluggable::class);

        Page::observe(Sluggable::class);

        PirepField::observe(Sluggable::class);
        PirepFieldValue::observe(Sluggable::class);

        Setting::observe(SettingObserver::class);
        Subfleet::observe(SubfleetObserver::class);
        User::observe(UserObserver::class);
    }
}
