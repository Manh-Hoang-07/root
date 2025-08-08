<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

        // Tối ưu: Chỉ kiểm tra token một lần
        $token = $this->getTokenFromRequest($request);
        
        // Nếu có token thì authenticate
        if ($token) {
            $this->authenticateWithToken($token);
        }

        return $next($request);
    }

    /**
     * Tối ưu: Lấy token từ request một cách hiệu quả
     */
    private function getTokenFromRequest(Request $request): ?string
    {
        // Ưu tiên Authorization header trước
        $token = $request->bearerToken();
        
        if (!$token) {
            // Thử query parameter
            $token = $request->query('token');
        }
        
        if (!$token) {
            // Cuối cùng thử cookie
            $token = $request->cookie('auth_token');
        }
        
        return $token;
    }

    /**
     * Tối ưu: Authenticate với token và cache
     */
    private function authenticateWithToken(string $token): void
    {
        // Cache token validation để tránh query database mỗi lần
        $cacheKey = 'auth_token_' . md5($token);
        
        $userId = Cache::remember($cacheKey, 300, function () use ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable) {
                return $accessToken->tokenable->id;
            }
            return null;
        });
        
        if ($userId) {
            // Load user và authenticate
            $user = \App\Models\User::find($userId);
            if ($user) {
                Auth::login($user);
            }
        }
    }
} 