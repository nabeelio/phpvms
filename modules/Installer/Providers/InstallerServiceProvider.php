<?php

namespace Modules\Installer\Providers;

use App\Services\ModuleService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Installer\Console\Commands\ImportFromClassicCommand;

class InstallerServiceProvider extends ServiceProvider
{
    protected $moduleSvc;

    /**
     * Boot the application events.
     */
    public function boot()
    {
        $this->moduleSvc = app(ModuleService::class);

        $this->registerCommands();
        $this->registerRoutes();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
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
            'as'         => 'installer.',
            'prefix'     => 'install',
            'middleware' => ['web'],
            'namespace'  => 'Modules\Installer\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../Http/Routes/install.php');
        });

        Route::group([
             'as'         => 'update.',
             'prefix'     => 'update',
             'middleware' => ['web'],
             'namespace'  => 'Modules\Installer\Http\Controllers',
         ], function () {
             $this->loadRoutesFrom(__DIR__.'/../Http/Routes/update.php');
         });

        Route::group([
            'as'         => 'importer.',
            'prefix'     => 'importer',
            'middleware' => ['web'],
            'namespace'  => 'Modules\Installer\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../Http/Routes/importer.php');
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('installer.php'),
        ], 'installer');

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
