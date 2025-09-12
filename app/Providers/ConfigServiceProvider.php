<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\ConfigHelper;
use App\Facades\ConfigFacade;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Đăng ký ConfigHelper như một singleton
        $this->app->singleton('app-config', function ($app) {
            return new ConfigHelper();
        });

        // Đăng ký alias cho facade
        $this->app->alias('app-config', ConfigHelper::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Không cần bootstrap gì đặc biệt cho API project
    }
}
