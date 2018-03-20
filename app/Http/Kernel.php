<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\InstalledCheck::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Spatie\Pjax\Middleware\FilterIfPjax::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            'json',
        ],
    ];
    protected $routeMiddleware = [
        'api.auth'   => \App\Http\Middleware\ApiAuth::class,
        'auth'       => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'   => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'        => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'json'       => \App\Http\Middleware\JsonResponse::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
