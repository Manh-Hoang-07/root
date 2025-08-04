<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.sanctum' => \App\Http\Middleware\Api\Authenticate::class,
            'role' => \App\Http\Middleware\Api\RoleMiddleware::class,
            'auto.auth' => \App\Http\Middleware\Api\AutoAuthMiddleware::class,
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
        ]);
        
        // Sử dụng middleware CORS tùy chỉnh
        $middleware->prepend(\App\Http\Middleware\CorsMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
