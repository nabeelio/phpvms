<?php
/**
 * Handle the authentication for the API layer
 */

namespace App\Http\Middleware;

use Auth;
use Cache;
use Closure;
use App\Models\User;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if Authorization header is in place
        if(!$request->header('Authorization')) {
            return $this->unauthorized();
        }

        // Try to find the user via API key. Cache this lookup
        $api_key = $request->header('Authorization');
        $user = Cache::remember(
            config('cache.keys.USER_API_KEY.key') . $api_key,
            config('cache.keys.USER_API_KEY.time'),
            function () use ($api_key) {
                return User::where('apikey', $api_key)->first();
            }
        );

        if(!$user) {
            return $this->unauthorized();
        }

        // Set the user to the request
        Auth::setUser($user);
        $request->merge(['user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    /**
     * Return an unauthorized message
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private function unauthorized()
    {
        return response([
            'error' => [
                'code' => '401',
                'http_code' => 'Unauthorized',
                'message' => 'Invalid or missing API key',
            ],
        ], 401);
    }
}
