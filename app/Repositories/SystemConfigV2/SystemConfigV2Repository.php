<?php

namespace App\Repositories\SystemConfigV2;

use App\Models\SystemConfigV2;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class SystemConfigV2Repository extends BaseRepository
{
    protected $cachePrefix = 'config_v2_';
    protected $cacheTtl = 3600; // 1 hour

    public function model()
    {
        return SystemConfigV2::class;
    }

    public function getByKey($key, $default = null)
    {
        $cacheKey = $this->cachePrefix . 'key_' . $key;
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($key, $default) {
            $config = $this->model->byKey($key)->first();
            return $config ? $config->value : $default;
        });
    }

    public function getByGroup($group)
    {
        $cacheKey = $this->cachePrefix . 'group_' . $group;
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($group) {
            return $this->model->byGroup($group)->get()->pluck('value', 'key')->toArray();
        });
    }

    public function updateGroupConfig($group, $data)
    {
        $updated = [];
        
        foreach ($data as $key => $value) {
            $config = $this->model->updateOrCreate(
                ['key' => $key, 'group' => $group],
                [
                    'value' => $value,
                    'group' => $group,
                    'is_public' => $this->isPublicKey($key, $group),
                    'description' => $this->getKeyDescription($key, $group),
                    'type' => $this->getKeyType($key, $group)
                ]
            );
            $updated[$key] = $config->value;
        }

        // Clear cache
        $this->clearGroupCache($group);

        return $updated;
    }

    public function updateByKey($key, $value)
    {
        $config = $this->model->where('key', $key)->first();
        
        if (!$config) {
            throw new \Exception("Config key '{$key}' không tồn tại");
        }

        $config->update(['value' => $value]);
        
        // Clear cache
        $this->clearCache($key);
        $this->clearGroupCache($config->group);

        return $config->value;
    }

    private function isPublicKey($key, $group)
    {
        $publicKeys = [
            'general' => ['app_name', 'site_name', 'site_logo', 'site_favicon', 'site_email', 'site_phone', 'site_address', 'site_description', 'timezone', 'language', 'items_per_page'],
            'email' => ['from_email', 'from_name']
        ];

        return in_array($key, $publicKeys[$group] ?? []);
    }

    private function getKeyDescription($key, $group)
    {
        $descriptions = [
            'general' => [
                'app_name' => 'Tên ứng dụng',
                'site_name' => 'Tên website',
                'site_logo' => 'Logo website',
                'site_favicon' => 'Favicon website',
                'site_email' => 'Email liên hệ',
                'site_phone' => 'Số điện thoại liên hệ',
                'site_address' => 'Địa chỉ website',
                'site_description' => 'Mô tả website',
                'timezone' => 'Múi giờ',
                'language' => 'Ngôn ngữ mặc định',
                'maintenance_mode' => 'Chế độ bảo trì',
                'items_per_page' => 'Số item mỗi trang'
            ],
            'email' => [
                'smtp_host' => 'SMTP Host',
                'smtp_port' => 'SMTP Port',
                'smtp_username' => 'SMTP Username',
                'smtp_password' => 'SMTP Password',
                'smtp_encryption' => 'SMTP Encryption',
                'from_email' => 'Email gửi đi',
                'from_name' => 'Tên người gửi'
            ]
        ];

        return $descriptions[$group][$key] ?? 'Cấu hình ' . $key;
    }

    private function getKeyType($key, $group)
    {
        $types = [
            'general' => [
                'items_per_page' => 'number',
                'maintenance_mode' => 'boolean',
                'site_address' => 'text',
                'site_description' => 'text'
            ],
            'email' => [
                'smtp_port' => 'number'
            ]
        ];

        return $types[$group][$key] ?? 'string';
    }

    private function clearCache($key = null)
    {
        if ($key) {
            Cache::forget($this->cachePrefix . 'key_' . $key);
        }
    }

    private function clearGroupCache($group)
    {
        Cache::forget($this->cachePrefix . 'group_' . $group);
    }

}
