<?php

namespace App\Services\Public\SystemConfig;

use App\Repositories\SystemConfig\SystemConfigRepository;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Cache;

class SystemConfigService
{
    protected $repository;
    protected string $cachePrefix = 'system_config_';
    protected int $cacheTtl = 3600; // 1 giờ

    public function __construct(SystemConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    // ==================== PUBLIC METHODS ====================

    /**
     * Lấy cấu hình chung cho public
     */
    public function getGeneralConfig(): array
    {
        $cacheKey = $this->cachePrefix . 'public_general_config';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $configs = $this->repository->getGroupAsArray('general');
            
            // Chỉ trả về các thông tin cần thiết cho public
            return [
                'site_name' => $configs['site_name'] ?? 'Website',
                'site_description' => $configs['site_description'] ?? '',
                'site_logo' => $configs['site_logo'] ?? '',
                'site_favicon' => $configs['site_favicon'] ?? '',
                'contact_email' => $configs['contact_email'] ?? '',
                'contact_phone' => $configs['contact_phone'] ?? '',
                'contact_address' => $configs['contact_address'] ?? '',
                'social_facebook' => $configs['social_facebook'] ?? '',
                'social_twitter' => $configs['social_twitter'] ?? '',
                'social_instagram' => $configs['social_instagram'] ?? '',
                'social_youtube' => $configs['social_youtube'] ?? '',
            ];
        });
    }

    /**
     * Lấy cấu hình SEO cho public
     */
    public function getSeoConfig(): array
    {
        $cacheKey = $this->cachePrefix . 'public_seo_config';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $configs = $this->repository->getGroupAsArray('seo');
            
            return [
                'meta_title' => $configs['meta_title'] ?? '',
                'meta_description' => $configs['meta_description'] ?? '',
                'meta_keywords' => $configs['meta_keywords'] ?? '',
                'og_title' => $configs['og_title'] ?? '',
                'og_description' => $configs['og_description'] ?? '',
                'og_image' => $configs['og_image'] ?? '',
            ];
        });
    }

    /**
     * Lấy cấu hình hiển thị cho public
     */
    public function getDisplayConfig(): array
    {
        $cacheKey = $this->cachePrefix . 'public_display_config';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $configs = $this->repository->getGroupAsArray('display');
            
            return [
                'posts_per_page' => $configs['posts_per_page'] ?? 10,
                'show_related_posts' => $configs['show_related_posts'] ?? true,
                'show_author_info' => $configs['show_author_info'] ?? true,
                'show_comment_count' => $configs['show_comment_count'] ?? true,
                'show_view_count' => $configs['show_view_count'] ?? true,
                'enable_search' => $configs['enable_search'] ?? true,
            ];
        });
    }

    /**
     * Lấy cấu hình email cho public
     */
    public function getEmailConfig(): array
    {
        $cacheKey = $this->cachePrefix . 'public_email_config';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $configs = $this->repository->getGroupAsArray('email');
            
            return [
                'from_email' => $configs['from_email'] ?? '',
                'from_name' => $configs['from_name'] ?? '',
            ];
        });
    }

    // ==================== UNIFIED METHODS ====================

    /**
     * Lấy config theo group (Public)
     */
    public function getByGroup(string $group): array
    {
        $cacheKey = "system_configs_group_array_{$group}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($group) {
            return $this->repository->getGroupAsArray($group);
        });
    }

    /**
     * Lấy config theo key (Public)
     */
    public function getByKey(string $key, $default = null)
    {
        $cacheKey = "system_config_key_{$key}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($key, $default) {
            return $this->repository->getByKey($key, $default);
        });
    }

    /**
     * Lấy config theo nhiều keys (Public)
     */
    public function getByKeys(array $keys): array
    {
        $cacheKey = "system_configs_keys_" . md5(implode(',', $keys));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($keys) {
            $configs = $this->repository->getByKeys($keys);
            $result = [];
            
            foreach ($configs as $config) {
                $result[$config->config_key] = $config->value;
            }
            
            return $result;
        });
    }

    /**
     * Lấy config public (Public)
     */
    public function getPublicConfigs(): array
    {
        $cacheKey = "system_configs_public";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            $configs = $this->repository->getPublicConfigs();
            $result = [];
            
            foreach ($configs as $config) {
                $result[$config->config_key] = $config->value;
            }
            
            return $result;
        });
    }

    // ==================== API RESPONSE METHODS ====================

    /**
     * Lấy config theo key với API response
     */
    public function getByKeyResponse(string $key, $default = null)
    {
        try {
            $value = $this->getByKey($key, $default);
            return ApiResponse::success($value, 'Lấy cấu hình thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi lấy cấu hình: ' . $e->getMessage());
        }
    }

    /**
     * Lấy cấu hình chung với API response
     */
    public function getGeneralConfigResponse()
    {
        try {
            $configs = $this->getGeneralConfig();
            return ApiResponse::success($configs, 'Lấy cấu hình hệ thống thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi lấy cấu hình hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Lấy cấu hình email với API response
     */
    public function getEmailConfigResponse()
    {
        try {
            $configs = $this->getEmailConfig();
            return ApiResponse::success($configs, 'Lấy cấu hình email thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi lấy cấu hình email: ' . $e->getMessage());
        }
    }

    // ==================== CACHE METHODS ====================

    /**
     * Xóa cache cấu hình
     */
    public function clearCache(): bool
    {
        try {
            $keys = [
                'public_general_config',
                'public_seo_config', 
                'public_display_config',
                'public_email_config',
                'system_configs_public'
            ];
            
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
