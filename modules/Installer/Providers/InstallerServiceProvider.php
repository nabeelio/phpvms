<?php

namespace Modules\Installer\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Nwidart\Modules\Support\Stub;
use Route;


class InstallerServiceProvider extends ServiceProvider
{
    protected $defer = false;
    protected $moduleSvc;

    /**
     * Boot the application events.
     */
    public function boot()
    {
        $this->moduleSvc = app('App\Services\ModuleService');

        $this->registerRoutes();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
    }

    /**
     * Register the routes
     */
    protected function registerRoutes()
    {
        Route::group([
            'as' => 'installer.',
            'prefix' => 'install',
            'middleware' => ['web'],
            'namespace' => 'Modules\Installer\Http\Controllers'
        ], function() {
            $this->loadRoutesFrom(__DIR__ . '/../Http/Routes/install.php');
        });

        Route::group([
             'as' => 'installer.',
             'prefix' => 'install',
             'middleware' => ['web'],
             'namespace' => 'Modules\Installer\Http\Controllers'
         ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../Http/Routes/update.php');
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('installer.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'installer'
        );
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/installer');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/installer';
        }, \Config::get('view.paths')), [$sourcePath]), 'installer');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/installer');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'installer');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'installer');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return [];
    }
}
