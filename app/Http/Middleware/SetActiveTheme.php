<?php

namespace App\Http\Middleware;

use App\Contracts\Middleware;
use Closure;
use Igaster\LaravelTheme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Read the current theme from the settings (set in admin), and set it
 */
class SetActiveTheme implements Middleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $theme = setting('general.theme');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $theme = 'default';
        }

        if (!empty($theme)) {
            Theme::set($theme);
        }

        return $next($request);
    }
}
