<?php

namespace App\Services\Public\SystemConfig;

use App\Services\Admin\SystemConfig\SystemConfigV2Service;
use Illuminate\Support\Facades\Cache;

class SystemConfigService
{
    protected SystemConfigV2Service $configService;
    protected int $cacheTtl = 3600; // 1 giờ

    public function __construct(SystemConfigV2Service $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Lấy cấu hình chung cho public
     */
    public function getGeneralConfig()
    {
        $cacheKey = 'public_general_config';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $response = $this->configService->getGeneralConfig();
            
            // Lấy data từ JsonResponse
            $config = $response->getData(true)['data'] ?? [];
            
            // Chỉ trả về các thông tin cần thiết cho public
            return [
                'site_name' => $config['site_name'] ?? 'Website',
                'site_description' => $config['site_description'] ?? '',
                'site_logo' => $config['site_logo'] ?? '',
                'site_favicon' => $config['site_favicon'] ?? '',
                'contact_email' => $config['contact_email'] ?? '',
                'contact_phone' => $config['contact_phone'] ?? '',
                'contact_address' => $config['contact_address'] ?? '',
                'social_facebook' => $config['social_facebook'] ?? '',
                'social_twitter' => $config['social_twitter'] ?? '',
                'social_instagram' => $config['social_instagram'] ?? '',
                'social_youtube' => $config['social_youtube'] ?? '',
            ];
        });
    }

    /**
     * Lấy cấu hình SEO
     */
    public function getSeoConfig()
    {
        $cacheKey = 'public_seo_config';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $response = $this->configService->getGeneralConfig();
            
            // Lấy data từ JsonResponse
            $config = $response->getData(true)['data'] ?? [];
            
            return [
                'meta_title' => $config['meta_title'] ?? '',
                'meta_description' => $config['meta_description'] ?? '',
                'meta_keywords' => $config['meta_keywords'] ?? '',
                'og_title' => $config['og_title'] ?? '',
                'og_description' => $config['og_description'] ?? '',
                'og_image' => $config['og_image'] ?? '',
            ];
        });
    }

    /**
     * Lấy cấu hình hiển thị
     */
    public function getDisplayConfig()
    {
        $cacheKey = 'public_display_config';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $response = $this->configService->getGeneralConfig();
            
            // Lấy data từ JsonResponse
            $config = $response->getData(true)['data'] ?? [];
            
            return [
                'posts_per_page' => $config['posts_per_page'] ?? 10,
                'show_related_posts' => $config['show_related_posts'] ?? true,
                'show_author_info' => $config['show_author_info'] ?? true,
                'show_comment_count' => $config['show_comment_count'] ?? true,
                'show_view_count' => $config['show_view_count'] ?? true,
                'enable_search' => $config['enable_search'] ?? true,
            ];
        });
    }

    /**
     * Lấy cấu hình theo key
     */
    public function getConfigByKey(string $key)
    {
        $cacheKey = "public_config_{$key}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($key) {
            $response = $this->configService->getByKey($key);
            
            // Lấy data từ JsonResponse
            $data = $response->getData(true)['data'] ?? null;
            
            return $data;
        });
    }

    /**
     * Xóa cache cấu hình
     */
    public function clearCache()
    {
        $keys = [
            'public_general_config',
            'public_seo_config',
            'public_display_config',
        ];
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        // Xóa cache config theo key
        Cache::forget('public_config_*');
        
        return true;
    }
}
