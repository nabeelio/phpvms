<?php
/**
 * https://stackoverflow.com/a/34894933
 */

namespace App\Http\Middleware;

use Closure;

class MeasureExecutionTime
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get the response
        $response = $next($request);
        if (!defined('LUMEN_START')) {
            return $response;
        }

        // Calculate execution time
        $executionTime = microtime(true) - LUMEN_START;

        // I assume you're using valid json in your responses
        // Then I manipulate them below
        $content = json_decode($response->getContent(), true) + [
                'execution_time' => $executionTime,
            ];

        // Change the content of your response
        $response->setData($content);

        // Return the response
        return $response;
    }
}
