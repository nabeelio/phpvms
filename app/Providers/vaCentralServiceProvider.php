<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use VaCentral\VaCentral;

/**
 * Bootstrap the vaCentral library
 * @package App\Providers
 */
class vaCentralServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (filled(config('vacentral.api_key'))) {
            VaCentral::setApiKey(config('vacentral.api_key'));
        }
    }
}
