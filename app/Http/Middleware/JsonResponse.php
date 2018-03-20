<?php
/**
 * Set the content type in the API layer
 */

namespace App\Http\Middleware;

use Closure;

class JsonResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
