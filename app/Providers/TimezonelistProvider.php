<?php

namespace App\Providers;

use App\Support\Timezonelist;
use Illuminate\Support\ServiceProvider;

class TimezonelistProvider extends ServiceProvider
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
            return new Timezonelist();
        });
    }
}
