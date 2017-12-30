<?php
/**
 * Handle the authentication for the API layer
 */

namespace App\Http\Middleware;

use Auth;
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
            return $this->unauthorized('Authorization header missing');
        }

        // Try to find the user via API key. Cache this lookup
        $api_key = $request->header('Authorization');
        $user = User::where('api_key', $api_key)->first();
        if($user === null) {
            return $this->unauthorized('User not found with key "'.$api_key.'"');
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
    private function unauthorized($details='')
    {
        return response([
            'error' => [
                'code' => '401',
                'http_code' => 'Unauthorized',
                'message' => 'Invalid or missing API key ('. $details .')',
            ],
        ], 401);
    }
}
