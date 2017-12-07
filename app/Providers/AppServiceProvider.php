<?php

namespace App\Providers;

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

        //\VaCentral\VaCentral::setVaCentralUrl(config('phpvms.vacentral_api_url'));
        if(!empty(config('phpvms.vacentral_api_key'))) {
            \VaCentral\VaCentral::setApiKey(config('phpvms.vacentral_api_key'));
        }

        # if there's a local.conf.php in the root, then merge that in
        if(file_exists(base_path('local.conf.php'))) {
            $local_conf = include base_path('local.conf.php');
            $config = $this->app['config']->get('phpvms', []);
            $this->app['config']->set(
                'phpvms',
                array_merge($config, $local_conf)
            );
        }
    }

    /**
     * Register any application services.
     */
    public function register()
    {

    }
}
