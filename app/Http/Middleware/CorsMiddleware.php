<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lấy cấu hình từ .env
        $allowedOrigin = env('CORS_ALLOWED_ORIGIN', 'http://localhost:3000');
        $allowedMethods = env('CORS_ALLOWED_METHODS', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
        $allowedHeaders = env('CORS_ALLOWED_HEADERS', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, X-XSRF-TOKEN');
        $allowCredentials = env('CORS_ALLOW_CREDENTIALS', 'true');
        $maxAge = env('CORS_MAX_AGE', '86400');

        // Xử lý preflight requests (OPTIONS)
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', $allowedMethods)
                ->header('Access-Control-Allow-Headers', $allowedHeaders)
                ->header('Access-Control-Allow-Credentials', $allowCredentials)
                ->header('Access-Control-Max-Age', $maxAge);
        }

        $response = $next($request);

        // Thêm CORS headers cho tất cả responses
        $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
        $response->headers->set('Access-Control-Allow-Methods', $allowedMethods);
        $response->headers->set('Access-Control-Allow-Headers', $allowedHeaders);
        $response->headers->set('Access-Control-Allow-Credentials', $allowCredentials);
        $response->headers->set('Access-Control-Max-Age', $maxAge);

        return $response;
    }
} 