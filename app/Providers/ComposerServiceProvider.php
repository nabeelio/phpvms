<?php

namespace App\Providers;

use App\Http\Composers\VersionComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Attach the version number to the admin sidebar
        View::composer('admin.sidebar', VersionComposer::class);
    }
}
