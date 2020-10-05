<?php

namespace App\Providers;

use App\Contracts\Modules\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use App\Console\Commands\ImportFromClassicCommand;

class ImporterServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerRoutes();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Register console commands
     */
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
            'namespace'  => 'App\Http\Controllers\Importer',
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
        $this->mergeConfigFrom(__DIR__.'/../../Config/importer.php', 'importer');
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/views/importer', 'importer');
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
}
