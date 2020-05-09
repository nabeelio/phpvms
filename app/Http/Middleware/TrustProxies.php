<?php

namespace App\Http\Middleware;

use Closure;
use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;

    /**
     * Set the client as trusted
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->setTrustedProxies([$request->getClientIp()], $this->getTrustedHeaderNames());
        $this->setTrustedProxyIpAddresses($request);

        return $next($request);
    }
}
