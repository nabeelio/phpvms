<?php

namespace App\Providers;

use App\Support\TimezonelistExtended;
use Illuminate\Support\ServiceProvider;

class ExtendedTimezonelistProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('timezonelist', function ($app) {
            return new TimezonelistExtended;
        });
    }
}
