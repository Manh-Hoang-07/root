<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiPerformanceLog
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
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Enable query log in development
        if (app()->environment('local')) {
            DB::enableQueryLog();
        }
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;
        
        // Log performance metrics
        $metrics = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'execution_time_ms' => round($executionTime, 2),
            'memory_usage_bytes' => $memoryUsage,
            'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'user_id' => Auth::check() ? Auth::id() : null,
            'ip' => $request->ip(),
        ];
        
        // Add query count in development
        if (app()->environment('local')) {
            $queryLog = DB::getQueryLog();
            $metrics['query_count'] = count($queryLog);
            $metrics['queries'] = $queryLog;
        }
        
        // Log based on performance
        if ($executionTime > 1000) { // > 1 second
            Log::warning('Slow API request detected', $metrics);
        } elseif ($executionTime > 500) { // > 500ms
            Log::info('API request performance', $metrics);
        } else {
            Log::debug('API request completed', $metrics);
        }
        
        // Add performance headers to response
        $response->headers->set('X-Execution-Time', round($executionTime, 2) . 'ms');
        $response->headers->set('X-Memory-Usage', round($memoryUsage / 1024 / 1024, 2) . 'MB');
        
        if (app()->environment('local')) {
            $response->headers->set('X-Query-Count', count(DB::getQueryLog()));
        }
        
        return $response;
    }
} 