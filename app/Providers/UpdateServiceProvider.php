<?php

namespace App\Providers;

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
            'middleware' => ['web', 'auth', 'ability:admin,admin-access'],
            'namespace'  => 'App\Http\Controllers\Updater',
        ], function () {
            Route::get('/', 'UpdateController@index')->name('index');

            Route::get('/step1', 'UpdateController@step1')->name('step1');
            Route::post('/step1', 'UpdateController@step1')->name('step1post');

            Route::post('/run-migrations', 'UpdateController@run_migrations')->name('run_migrations');
            Route::get('/complete', 'UpdateController@complete')->name('complete');

            // Routes for the update downloader
            Route::get('/downloader', 'UpdateController@updater')->name('updater');
            Route::post('/downloader', 'UpdateController@update_download')->name('update_download');
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/updater.php', 'updater');
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/views/updater', 'updater');
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
