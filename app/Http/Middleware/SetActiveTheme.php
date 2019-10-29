<?php

namespace App\Http\Middleware;

use Closure;
use Igaster\LaravelTheme\Facades\Theme;

/**
 * Read the current theme from the settings (set in admin), and set it
 */
class SetActiveTheme
{
    public function handle($request, Closure $next)
    {
        $theme = setting('general.theme');
        if (!empty($theme)) {
            Theme::set($theme);
        }

        return $next($request);
    }
}
