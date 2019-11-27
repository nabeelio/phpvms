<?php

namespace App\Http\Middleware;

use App\Contracts\Middleware;
use Closure;
use Igaster\LaravelTheme\Facades\Theme;
use Illuminate\Http\Request;

/**
 * Read the current theme from the settings (set in admin), and set it
 */
class SetActiveTheme implements Middleware
{
    public function handle(Request $request, Closure $next)
    {
        $theme = setting('general.theme');
        if (!empty($theme)) {
            Theme::set($theme);
        }

        return $next($request);
    }
}
