<?php
/**
 * Handle the authentication for the API layer
 */

namespace App\Http\Middleware;

use Closure;

class InstalledCheck
{
    /**
     * Check the app.key to see whether we're installed or not
     * 
     */
    public function handle($request, Closure $next)
    {
        if (config('app.key') === 'NOT_INSTALLED') {
            return view('system.errors.not_installed');
        }

        return $next($request);
    }
}
