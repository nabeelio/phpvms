<?php

namespace App\Providers;

use Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Repositories\SettingRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->app->bind('setting', SettingRepository::class);
    }

    /**
     * Register any application services.
     */
    public function register()
    {

    }
}
