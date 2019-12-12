<?php

namespace Modules\Importer\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Importer\Console\Commands\ImportFromClassicCommand;

class ImporterServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot()
    {
        $this->registerCommands();
        $this->registerRoutes();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    protected function registerCommands()
    {
        $this->commands([
            ImportFromClassicCommand::class,
        ]);
    }

    /**
     * Register the routes
     */
    protected function registerRoutes()
    {
        Route::group([
            'as'         => 'importer.',
            'prefix'     => 'importer',
            'middleware' => ['web'],
            'namespace'  => 'Modules\Importer\Http\Controllers',
        ], function () {
            Route::get('/', 'ImporterController@index')->name('index');
            Route::post('/config', 'ImporterController@config')->name('config');
            Route::post('/dbtest', 'ImporterController@dbtest')->name('dbtest');

            // Run the actual importer process. Additional middleware
            Route::post('/run', 'ImporterController@run')->middleware('api')->name('run');

            Route::post('/complete', 'ImporterController@complete')->name('complete');
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'importer');
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/importer');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([$sourcePath => $viewPath], 'views');

        $paths = array_map(
            function ($path) {
                return $path.'/modules/importer';
            },
            \Config::get('view.paths')
        );

        $paths[] = $sourcePath;
        $this->loadViewsFrom($paths, 'importer');
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/importer');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'importer');
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'importer');
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
