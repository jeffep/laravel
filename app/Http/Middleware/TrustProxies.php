<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustProxies extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

//CREATED BY COPILOT:
/**
 * The trusted proxies for this application.
 *
 * @var array|string|null
 */
 protected $proxies = '*'; // Trust all proxies

/**
 * The headers that should be used to detect proxies.
 *
 * @var int
 */
 protected $headers = Request::HEADER_X_FORWARDED_ALL;

/**
 * Handle an incoming request.
 *
 * @param \Illuminate\Http\Request $request
 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
 * @return \Symfony\Component\HttpFoundation\Response
 */


    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
