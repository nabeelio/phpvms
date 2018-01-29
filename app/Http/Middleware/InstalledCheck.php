<?php
/**
 * Handle the authentication for the API layer
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InstalledCheck
{
    /**
     * Check the app.key to see whether we're installed or not
     *
     * If the default key is set and we're not in any of the installer routes
     * show the message that we need to be installed
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('app.key') === 'base64:zdgcDqu9PM8uGWCtMxd74ZqdGJIrnw812oRMmwDF6KY='
            && !$request->is(['install', 'install/*'])
            && !$request->is(['update', 'update/*'])
        ) {
            return view('system.errors.not_installed');
        }

        return $next($request);
    }
}
