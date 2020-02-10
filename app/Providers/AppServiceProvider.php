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
use App\Models\PirepField;
use App\Models\PirepFieldValue;
use App\Models\Setting;
use App\Models\Subfleet;
use App\Models\User;
use App\Repositories\SettingRepository;
use App\Services\ModuleService;
use App\Support\Utils;
use Codedge\Updater\UpdaterManager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Updater\Lib\VmsRepositoryType;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        /*Carbon::serializeUsing(function ($carbon) {
            return $carbon->format('U');
        });*/

        $this->app->bind('setting', SettingRepository::class);

        View::share('moduleSvc', app(ModuleService::class));

        // Model observers
        Aircraft::observe(AircraftObserver::class);
        Airport::observe(AirportObserver::class);
        Journal::observe(JournalObserver::class);
        JournalTransaction::observe(JournalTransactionObserver::class);

        Flight::observe(FlightObserver::class);
        FlightField::observe(Sluggable::class);
        FlightFieldValue::observe(Sluggable::class);

        PirepField::observe(Sluggable::class);
        PirepFieldValue::observe(Sluggable::class);

        Setting::observe(SettingObserver::class);
        Subfleet::observe(SubfleetObserver::class);
        User::observe(UserObserver::class);

        // $manager = app(UpdaterManager::class);
        // $manager->extend('vms', function($app) use ($manager) {
        //     return $manager->sourceRepository(new VmsRepositoryType);
        // });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only load the IDE helper if it's included and enabled
        if (config('app.debug') === true) {
            /* @noinspection NestedPositiveIfStatementsInspection */
            /* @noinspection PhpFullyQualifiedNameUsageInspection */
            if (class_exists(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class)) {
                /* @noinspection PhpFullyQualifiedNameUsageInspection */
                $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            }

            if (config('app.debug_toolbar') === true) {
                Utils::enableDebugToolbar();
            } else {
                Utils::disableDebugToolbar();
            }
        }
    }
}
