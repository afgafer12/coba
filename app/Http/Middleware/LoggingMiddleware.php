<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LoggingMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        info("[REQUEST to {$request->fullUrl()}] " . json_encode($request->all(), JSON_PRETTY_PRINT));
        $response = $next($request);
        info("[RESPONSE from {$request->fullUrl()}] " . json_encode(json_decode($response->content()), JSON_PRETTY_PRINT));
        return $response;
    }
}
