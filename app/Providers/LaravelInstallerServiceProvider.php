<?php

namespace App\Providers;

use RachidLaasri\LaravelInstaller\Providers\LaravelInstallerServiceProvider as ServiceProvider;


class LaravelInstallerServiceProvider extends ServiceProvider
{
       /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishFiles();
        $route_path = base_path('/vendor/rachidlaasri/laravel-installer/src/Routes/web.php');
        $this->loadRoutesFrom($route_path);
    }
}
