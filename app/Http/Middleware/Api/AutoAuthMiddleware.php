<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AutoAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Nếu đã authenticated thì bỏ qua
        if (Auth::check()) {
            return $next($request);
        }

        // 1. Thử lấy token từ Authorization header
        $token = $request->bearerToken();
        
        // 2. Nếu không có, thử lấy từ query parameter
        if (!$token) {
            $token = $request->query('token');
        }
        
        // 3. Nếu không có, thử lấy từ cookie
        if (!$token) {
            $token = $request->cookie('auth_token');
        }

        // Nếu có token thì authenticate
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
                if ($user) {
                    Auth::login($user);
                }
            }
        }

        return $next($request);
    }
} 