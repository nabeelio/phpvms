<?php

namespace App\Providers;

use App\Contracts\Metar;
use Illuminate\Support\ServiceProvider;

class WeatherServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(
            Metar::class,
            config('phpvms.metar')
        );
    }
}
