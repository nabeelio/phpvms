<?php
namespace App\Providers;

use App\Repositories\SettingRepository;
use App\Services\ModuleService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use View;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        $this->app->bind('setting', SettingRepository::class);

        View::share('moduleSvc', app(ModuleService::class));
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }
}
