<?php

namespace App\Providers;

use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
