<?php

namespace Modules\Sample\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Route;


class SampleServiceProvider extends ServiceProvider
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

        $this->registerLinks();

        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }

    /**
     * Add module links here
     */
    public function registerLinks()
    {
        // Show this link if logged in
        // $this->moduleSvc->addFrontendLink('Sample', '/sample', '', $logged_in=true);

        // Admin links:
        $this->moduleSvc->addAdminLink('Sample', '/sample/admin');
    }

    /**
     * Register the routes
     */
    protected function registerRoutes()
    {
        Route::group([
            'as' => 'sample.',
            'prefix' => 'sample',
            // If you want a RESTful module, change this to 'api'
            'middleware' => ['web'],
            'namespace' => 'Modules\Sample\Http\Controllers'
        ], function() {
            $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('sample.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'sample'
        );
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/sample');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/sample';
        }, \Config::get('view.paths')), [$sourcePath]), 'sample');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/sample');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'sample');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'sample');
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
