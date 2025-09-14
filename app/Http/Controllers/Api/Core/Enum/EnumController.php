<?php

namespace App\Http\Controllers\Api\Core\Enum;

use App\Http\Controllers\Controller;
use App\Services\Core\Enum\EnumService;
use App\Traits\ResponseTrait;
use App\Traits\LoggingTrait;
use Illuminate\Http\JsonResponse;
use Exception;

class EnumController extends Controller
{
    use ResponseTrait;
    use LoggingTrait;

    protected EnumService $enumService;
    protected int $cacheTtl = 86400; // 24 giờ

    /**
     * Constructor
     */
    public function __construct(EnumService $enumService)
    {
        $this->enumService = $enumService;
    }

    /**
     * Lấy danh sách enum theo type
     */
    public function get(string $type): JsonResponse
    {
        try {
            $data = $this->enumService->getEnumsWithCache($type, $this->cacheTtl);
            
            if ($data->isEmpty()) {
                return $this->apiResponse(false, null, 'Loại enum không hợp lệ', 400);
            }

            return $this->apiResponse(true, $data->toArray(), 'Lấy danh sách enum thành công');
        } catch (Exception $e) {
            $this->logError('GetEnums', $e, ['type' => $type]);
            return $this->apiResponse(false, null, 'Không thể lấy danh sách enum', 500);
        }
    }

    /**
     * Lấy danh sách tất cả enum types có sẵn
     */
    public function getTypes(): JsonResponse
    {
        try {
            $types = $this->enumService->getAvailableTypes();
            return $this->apiResponse(true, $types->toArray(), 'Lấy danh sách enum types thành công');
        } catch (Exception $e) {
            $this->logError('GetEnumTypes', $e);
            return $this->apiResponse(false, null, 'Không thể lấy danh sách enum types', 500);
        }
    }

    /**
     * Clear cache cho một enum type cụ thể
     */
    public function clearCache(string $type): JsonResponse
    {
        try {
            $success = $this->enumService->clearCache($type);
            
            if (!$success) {
                return $this->apiResponse(false, null, 'Không thể xóa cache', 500);
            }

            return $this->apiResponse(true, null, "Đã xóa cache cho enum type: {$type}");
        } catch (Exception $e) {
            $this->logError('ClearEnumCache', $e, ['type' => $type]);
            return $this->apiResponse(false, null, 'Không thể xóa cache', 500);
        }
    }

    /**
     * Clear tất cả cache enum
     */
    public function clearAllCache(): JsonResponse
    {
        try {
            $success = $this->enumService->clearAllCache();
            
            if (!$success) {
                return $this->apiResponse(false, null, 'Không thể xóa tất cả cache', 500);
            }

            return $this->apiResponse(true, null, 'Đã xóa tất cả cache enum');
        } catch (Exception $e) {
            $this->logError('ClearAllEnumCache', $e);
            return $this->apiResponse(false, null, 'Không thể xóa tất cả cache', 500);
        }
    }
} 