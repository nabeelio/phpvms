<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        # if there's a local.conf.php in the root, then merge that in
        if(file_exists(base_path('local.conf.php'))) {
            $local_conf = include(base_path('local.conf.php'));
            $config = $this->app['config']->get('phpvms', []);
            $this->app['config']->set(
                'phpvms',
                array_merge($config, $local_conf)
            );
        }
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

        $this->app->bind('App\Services\PilotService', function($app) {
            return new \App\Services\PilotService();
        });

        $this->app->bind('App\Services\PIREPService', function($app) {
            return new \App\Services\PIREPService();
        });

    }
}
