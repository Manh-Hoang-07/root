<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Core\CacheService;
use Laravel\Sanctum\PersonalAccessToken;

class GlobalAuthMiddleware
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

        // Chỉ áp dụng cho API routes
        if (!$this->isApiRoute($request)) {
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
     * Kiểm tra xem request có phải là API route không
     */
    private function isApiRoute(Request $request): bool
    {
        return str_starts_with($request->path(), 'api/');
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

        $userId = CacheService::get($cacheKey, 'auth');
        if ($userId === null) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable) {
                $userId = $accessToken->tokenable->id;
                CacheService::put($cacheKey, $userId, 300, 'auth'); // Cache 5 phút
            }
        }

        if ($userId) {
            // Load user và authenticate
            $user = \App\Models\User::find($userId);
            if ($user) {
                Auth::login($user);
            }
        }
    }
}
