<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
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
     */
    public function register()
    {

    }
}
