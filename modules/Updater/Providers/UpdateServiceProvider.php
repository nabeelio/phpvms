<?php

namespace Modules\Updater\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UpdateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Register the routes
     */
    protected function registerRoutes()
    {
        Route::group([
            'as'         => 'update.',
            'prefix'     => 'update',
            'middleware' => ['auth', 'ability:admin,admin-access', 'web'],
            'namespace'  => 'Modules\Updater\Http\Controllers',
        ], function () {
             Route::get('/', 'UpdateController@index')->name('index');

             Route::get('/step1', 'UpdateController@step1')->name('step1');
             Route::post('/step1', 'UpdateController@step1')->name('step1');

             Route::post('/run-migrations', 'UpdateController@run_migrations')->name('run_migrations');
             Route::get('/complete', 'UpdateController@complete')->name('complete');
         });
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'updater');
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/updater');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $paths = array_map(
            function ($path) {
                return $path.'/modules/updater';
            },
            \Config::get('view.paths')
        );

        $paths[] = $sourcePath;
        $this->loadViewsFrom($paths, 'updater');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/updater');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'updater');
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'updater');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
