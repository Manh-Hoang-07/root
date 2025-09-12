<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class EnumController extends Controller
{
    use ResponseTrait;
    /**
     * Lấy danh sách enum theo type
     */
    public function get(Request $request, string $type): JsonResponse
    {
        // Cache key dựa trên type
        $cacheKey = "enums_{$type}";
        
        // Cache trong 24 giờ (86400 giây)
        $data = Cache::remember($cacheKey, 86400, function () use ($type) {
            $enums = [];

            // Chuyển type về snake_case để hỗ trợ cả PascalCase/CamelCase
            $type = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $type));

            switch ($type) {
                case 'user_status':
                    $enums = \App\Enums\UserStatus::cases();
                    break;
                case 'gender':
                    $enums = \App\Enums\Gender::cases();
                    break;
                case 'basic_status':
                    $enums = \App\Enums\BasicStatus::cases();
                    break;
                case 'role_status':
                    $enums = \App\Enums\RoleStatus::cases();
                    break;
                            case 'product_status':
                $enums = \App\Enums\ProductStatus::cases();
                break;
            case 'order_status':
                $enums = \App\Enums\OrderStatus::cases();
                break;
            case 'variant_status':
                $enums = \App\Enums\VariantStatus::cases();
                break;
            default:
                return null; // Return null để không cache error cases
            }

            return collect($enums)->map(function ($enum) {
                return [
                    'id' => $enum->value,
                    'name' => $enum->label(),
                    'value' => $enum->value,
                    'label' => $enum->label()
                ];
            });
        });

        // Nếu data là null (enum type không hợp lệ), trả về error
        if ($data === null) {
            return $this->apiResponse(false, null, 'Loại enum không hợp lệ.', 400);
        }

        return $this->apiResponse(true, $data, 'Lấy danh sách enum thành công.');
    }


    /**
     * Clear cache cho một enum type cụ thể
     */
    public function clearCache(string $type): JsonResponse
    {
        $cacheKey = "enums_{$type}";
        Cache::forget($cacheKey);
        
        return $this->apiResponse(true, null, "Đã xóa cache cho enum type: {$type}");
    }

    /**
     * Clear tất cả cache enum
     */
    public function clearAllCache(): JsonResponse
    {
        $enumTypes = ['user_status', 'gender', 'basic_status', 'role_status', 'product_status'];
        
        foreach ($enumTypes as $type) {
            $cacheKey = "enums_{$type}";
            Cache::forget($cacheKey);
        }
        
        return $this->apiResponse(true, null, 'Đã xóa tất cả cache enum');
    }
} 