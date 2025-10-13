<?php

namespace App\Libraries\Public;

use App\Services\Core\SystemConfig\SystemConfigService;
use App\Libraries\Core\CacheService;
use Illuminate\Support\Facades\App;

class SystemConfig
{
    /**
     * Lấy tất cả cấu hình hệ thống từ group general (có cache)
     */
    public static function getGeneralConfig(): array
    {
        // Lấy từ cache trước
        $cached = CacheService::get('general_config', 'system');
        if ($cached !== null) {
            return $cached;
        }

        // Nếu không có cache thì lấy từ database
        $configService = App::make(SystemConfigService::class);
        $configs = $configService->getByGroup('general', true);
        
        $result = [];
        foreach ($configs as $config) {
            if (!empty($config['key']) && isset($config['value'])) {
                $result[$config['key']] = $config['value'];
            }
        }
        
        // Cache lại
        CacheService::put('general_config', $result, 3600, 'system');
        
        return $result;
    }

    /**
     * Lấy cấu hình hệ thống theo key từ group general
     */
    public static function getGeneralConfigByKey(string $key, string $default = null): ?string
    {
        $configs = self::getGeneralConfig();
        return $configs[$key] ?? $default;
    }

    /**
     * Lấy tên ứng dụng từ cấu hình hệ thống
     */
    public static function getName(): string
    {
        return self::getGeneralConfigByKey('name');
    }

    /**
     * Lấy email từ hệ thống
     */
    public static function getAppEmail(): string
    {
        return self::getGeneralConfigByKey('email');
    }
}
