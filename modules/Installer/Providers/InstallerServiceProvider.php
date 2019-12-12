<?php

namespace Modules\Installer\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
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
            'as'         => 'installer.',
            'prefix'     => 'install',
            'middleware' => ['web'],
            'namespace'  => 'Modules\Installer\Http\Controllers',
        ], function () {
            Route::get('/', 'InstallerController@index')->name('index');
            Route::post('/dbtest', 'InstallerController@dbtest')->name('dbtest');

            Route::get('/step1', 'InstallerController@step1')->name('step1');
            Route::post('/step1', 'InstallerController@step1')->name('step1');

            Route::get('/step2', 'InstallerController@step2')->name('step2');
            Route::post('/envsetup', 'InstallerController@envsetup')->name('envsetup');
            Route::get('/dbsetup', 'InstallerController@dbsetup')->name('dbsetup');

            Route::get('/step3', 'InstallerController@step3')->name('step3');
            Route::post('/usersetup', 'InstallerController@usersetup')->name('usersetup');

            Route::get('/complete', 'InstallerController@complete')->name('complete');
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'installer');
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/installer');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $paths = array_map(
            function ($path) {
                return $path.'/modules/installer';
            },
            \Config::get('view.paths')
        );

        $paths[] = $sourcePath;
        $this->loadViewsFrom($paths, 'installer');
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
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'installer');
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
