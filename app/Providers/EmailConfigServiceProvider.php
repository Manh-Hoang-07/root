<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Core\Email\EmailService;
use App\Services\Core\SystemConfig\SystemConfigService;
use Illuminate\Support\Facades\Log;

class EmailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService(
                $app->make(SystemConfigService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Chỉ load cấu hình email khi không phải trong console command
            if (!$this->app->runningInConsole()) {
                $emailService = $this->app->make(EmailService::class);
                $emailService->getConfig();
            }
        } catch (\Exception $e) {
            // Log lỗi nhưng không làm crash ứng dụng
            Log::error('Không thể load cấu hình email từ database: ' . $e->getMessage());
        }
    }
}
