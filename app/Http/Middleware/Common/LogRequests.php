<?php

namespace App\Http\Middleware\Common;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $startTime;
        
        Log::info('Request processed', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'duration' => round($duration * 1000, 2) . 'ms',
            'status' => $response->getStatusCode(),
            'user_id' => $request->user()?->id
        ]);
        
        return $response;
    }
} 