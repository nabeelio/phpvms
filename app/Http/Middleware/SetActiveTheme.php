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
    private static $skip = [
        'admin',
        'admin/*',
        'api',
        'api/*',
        'importer',
        'importer/*',
        'install',
        'install/*',
        'update',
        'update/*',
    ];

    /**
     * Handle the request
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->setTheme($request);
        return $next($request);
    }

    /**
     * Set the theme for the current middleware
     *
     * @param \Illuminate\Http\Request $request
     */
    public function setTheme(Request $request)
    {
        if ($request->is(self::$skip)) {
            return;
        }

        try {
            $theme = setting('general.theme', 'default');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $theme = 'default';
        }

        if (!empty($theme)) {
            Theme::set($theme);
        }
    }
}
