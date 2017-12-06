<?php

namespace Modules\Vacentral\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Route;


class VacentralServiceProvider extends ServiceProvider
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
        // $this->moduleSvc->addFrontendLink('Vacentral', '/vacentral', '', $logged_in=true);

        // Admin links:
        $this->moduleSvc->addAdminLink('Vacentral', '/vacentral/admin');
    }

    /**
     * Register the routes
     */
    protected function registerRoutes()
    {
        Route::group([
            'as' => 'vacentral.',
            'prefix' => 'vacentral',
            // If you want a RESTful module, change this to 'api'
            'middleware' => ['web'],
            'namespace' => 'Modules\Vacentral\Http\Controllers'
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
            __DIR__.'/../Config/config.php' => config_path('vacentral.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'vacentral'
        );
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/vacentral');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/vacentral';
        }, \Config::get('view.paths')), [$sourcePath]), 'vacentral');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/vacentral');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'vacentral');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'vacentral');
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
