<?php

namespace App\Providers;

use App\Http\Composers\PageLinksComposer;
use App\Http\Composers\VersionComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', PageLinksComposer::class);
        View::composer('admin.sidebar', VersionComposer::class);
    }
}
