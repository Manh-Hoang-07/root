<?php

namespace App\Services\Core\Enum;

use App\Enums\UserStatus;
use App\Enums\Gender;
use App\Enums\BasicStatus;
use App\Enums\RoleStatus;
use App\Enums\ProductStatus;
use App\Enums\OrderStatus;
use App\Enums\VariantStatus;
use App\Enums\ContactStatus;
use App\Enums\PostStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EnumService
{
    /**
     * Mapping các enum classes
     */
    protected array $enumMap = [
        'user_status' => UserStatus::class,
        'gender' => Gender::class,
        'basic_status' => BasicStatus::class,
        'role_status' => RoleStatus::class,
        'product_status' => ProductStatus::class,
        'order_status' => OrderStatus::class,
        'variant_status' => VariantStatus::class,
        'contact_status' => ContactStatus::class,
        'post_status' => PostStatus::class,
    ];

    /**
     * Lấy danh sách enum theo type
     */
    public function getEnums(string $type): Collection
    {
        $normalizedType = $this->normalizeType($type);
        
        if (!isset($this->enumMap[$normalizedType])) {
            return collect();
        }

        $enumClass = $this->enumMap[$normalizedType];
        $enums = $enumClass::cases();

        return collect($enums)->map(function ($enum) {
            return [
                'id' => $enum->value,
                'name' => $enum->label(),
                'value' => $enum->value,
                'label' => $enum->label()
            ];
        });
    }

    /**
     * Lấy tất cả enum types có sẵn
     */
    public function getAvailableTypes(): Collection
    {
        return collect(array_keys($this->enumMap));
    }

    /**
     * Kiểm tra enum type có hợp lệ không
     */
    public function isValidType(string $type): bool
    {
        $normalizedType = $this->normalizeType($type);
        return isset($this->enumMap[$normalizedType]);
    }

    /**
     * Lấy enum theo type với cache
     */
    public function getEnumsWithCache(string $type, int $cacheTtl = 86400): Collection
    {
        $normalizedType = $this->normalizeType($type);
        $cacheKey = "enums_{$normalizedType}";

        return Cache::remember($cacheKey, $cacheTtl, function () use ($type) {
            return $this->getEnums($type);
        });
    }

    /**
     * Xóa cache cho một enum type
     */
    public function clearCache(string $type): bool
    {
        $normalizedType = $this->normalizeType($type);
        $cacheKey = "enums_{$normalizedType}";
        
        return Cache::forget($cacheKey);
    }

    /**
     * Xóa tất cả cache enum
     */
    public function clearAllCache(): bool
    {
        $success = true;
        
        foreach (array_keys($this->enumMap) as $type) {
            if (!$this->clearCache($type)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Chuẩn hóa type name (hỗ trợ cả PascalCase/CamelCase)
     */
    protected function normalizeType(string $type): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $type));
    }

}
