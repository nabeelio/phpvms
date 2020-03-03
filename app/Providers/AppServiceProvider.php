<?php

namespace App\Providers;

use App\Services\ModuleService;
use App\Support\Utils;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        View::share('moduleSvc', app(ModuleService::class));

        // if (!empty(config('app.url'))) {
        //     URL::forceRootUrl(config('app.url'));
        // }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only load the IDE helper if it's included and enabled
        if (config('app.debug') === true) {
            /* @noinspection NestedPositiveIfStatementsInspection */
            /* @noinspection PhpFullyQualifiedNameUsageInspection */
            if (class_exists(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class)) {
                /* @noinspection PhpFullyQualifiedNameUsageInspection */
                $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            }

            if (config('app.debug_toolbar') === true) {
                Utils::enableDebugToolbar();
            } else {
                Utils::disableDebugToolbar();
            }
        }
    }
}
