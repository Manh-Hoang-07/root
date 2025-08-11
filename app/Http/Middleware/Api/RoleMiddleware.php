<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        return $next($request);
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Vui lòng đăng nhập.',
                'error' => 'UNAUTHENTICATED'
            ], 401);
        }

        if (!$request->user()->hasRole($role)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Bạn không có quyền truy cập.',
                'error' => 'FORBIDDEN',
                'required_role' => $role,
                'user_roles' => $request->user()->getRoleNames()
            ], 403);
        }

        return $next($request);
    }
} 