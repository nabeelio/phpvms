<?php

namespace Modules\Vacentral\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class VacentralServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot()
    {
        $this->registerConfig();
        //$this->registerTranslations();

        //$this->registerFactories();
        //$this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
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
