<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class EnumCacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Clear enum cache khi có thay đổi trong database
        // Có thể thêm các event listeners khác tùy theo nhu cầu
        Event::listen('eloquent.saved: App\Models\User', function () {
            $this->clearEnumCache('user_status');
        });

        Event::listen('eloquent.saved: App\Models\Role', function () {
            $this->clearEnumCache('role_status');
        });

        Event::listen('eloquent.saved: App\Models\Product', function () {
            $this->clearEnumCache('product_status');
        });

        Event::listen('eloquent.saved: App\Models\Order', function () {
            $this->clearEnumCache('order_status');
        });

        Event::listen('eloquent.saved: App\Models\Variant', function () {
            $this->clearEnumCache('variant_status');
        });
    }

    /**
     * Clear cache cho enum type cụ thể
     */
    private function clearEnumCache(string $type): void
    {
        $cacheKey = "enums_{$type}";
        Cache::forget($cacheKey);
    }

    /**
     * Clear tất cả cache enum
     */
    public static function clearAllEnumCache(): void
    {
        $enumTypes = ['user_status', 'gender', 'basic_status', 'role_status', 'product_status', 'order_status', 'variant_status'];
        
        foreach ($enumTypes as $type) {
            $cacheKey = "enums_{$type}";
            Cache::forget($cacheKey);
        }
    }
} 