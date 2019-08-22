<?php

namespace App\Providers;

use App\Contracts\AirportLookup;
use App\Contracts\Metar;
use Illuminate\Support\ServiceProvider;

class BindServiceProviders extends ServiceProvider
{
    public function boot(): void
    {
        /*
         * Bind the class used to fullfill the Metar class contract
         */
        $this->app->bind(
            Metar::class,
            config('phpvms.metar_lookup')
        );

        /*
         * Bind the class used to fullfill the AirportLookup class contract
         */
        $this->app->bind(
            AirportLookup::class,
            config('phpvms.airport_lookup')
        );
    }
}
