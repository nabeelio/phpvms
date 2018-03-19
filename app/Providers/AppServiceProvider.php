<?php

namespace App\Providers;

use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Journal;
use App\Models\JournalTransaction;
use App\Models\Observers\AircraftObserver;
use App\Models\Observers\AirportObserver;
use App\Models\Observers\JournalObserver;
use App\Models\Observers\JournalTransactionObserver;
use App\Models\Observers\PirepFieldObserver;
use App\Models\Observers\SettingObserver;
use App\Models\PirepField;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use App\Services\ModuleService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use View;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        $this->app->bind('setting', SettingRepository::class);

        View::share('moduleSvc', app(ModuleService::class));

        // Model observers
        Aircraft::observe(AircraftObserver::class);
        Airport::observe(AirportObserver::class);
        Journal::observe(JournalObserver::class);
        JournalTransaction::observe(JournalTransactionObserver::class);
        PirepField::observe(PirepFieldObserver::class);
        Setting::observe(SettingObserver::class);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }
}
