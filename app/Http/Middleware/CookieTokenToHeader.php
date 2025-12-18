<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CookieTokenToHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
{
    if (!$request->bearerToken() && $request->hasCookie('api_token')) {
        $request->headers->set(
            'Authorization',
            'Bearer ' . $request->cookie('api_token')
        );
    }

    return $next($request);
}

}
