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

        # if there's a local.conf.php in the root, then merge that in
        /*if(file_exists(base_path('config.php'))) {
            $local_conf = include base_path('config.php');

            foreach($local_conf as $namespace => $override_config) {
                $config = $this->app['config']->get($namespace, []);
                $update_config = array_merge_recursive($config, $override_config);
                $this->app['config']->set($namespace, $update_config);
            }
        }*/
    }

    /**
     * Register any application services.
     */
    public function register()
    {

    }
}
