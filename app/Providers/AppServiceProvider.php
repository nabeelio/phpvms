<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\AircraftService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // bind all the app services...
        $this->app->bind('App\Services\AircraftService', function($app) {
            return new \App\Services\AircraftService();
        });

        $this->app->bind('App\Services\AircraftFareService', function($app) {
            return new \App\Services\AircraftFareService();
        });

        if (in_array($this->app->environment(), ['local', 'dev', 'unittest'])) {
            $this->app->register(\Bpocallaghan\Generators\GeneratorsServiceProvider::class);
        }
    }
}
