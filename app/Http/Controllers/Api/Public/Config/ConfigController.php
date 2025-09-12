<?php

namespace App\Http\Controllers\Api\Public\Config;

use App\Http\Controllers\Api\BaseController;
use App\Helpers\ConfigHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends BaseController
{
    /**
     * Lấy tất cả cấu hình public
     */
    public function getPublicConfigs(): JsonResponse
    {
        try {
            $configs = ConfigHelper::getPublicConfigs();
            
            return $this->successResponse(
                $configs,
                'Lấy cấu hình public thành công'
            );
        } catch (\Exception $e) {
            $this->logError('GetPublicConfigs', $e);
            return $this->errorResponse('Không thể tải cấu hình public');
        }
    }

    /**
     * Lấy cấu hình theo group
     */
    public function getGroupConfig(string $group): JsonResponse
    {
        try {
            $configs = ConfigHelper::getGroup($group);
            
            return $this->successResponse(
                $configs,
                "Lấy cấu hình nhóm {$group} thành công"
            );
        } catch (\Exception $e) {
            $this->logError('GetGroupConfig', $e);
            return $this->errorResponse("Không thể tải cấu hình nhóm {$group}");
        }
    }

    /**
     * Lấy cấu hình theo nhiều groups
     */
    public function getMultipleGroups(Request $request): JsonResponse
    {
        try {
            $groups = $request->input('groups', []);
            
            if (empty($groups)) {
                return $this->successResponse([], 'Không có groups nào được truyền');
            }
            
            $configs = ConfigHelper::getMultipleGroups($groups);
            
            return $this->successResponse(
                $configs,
                'Lấy cấu hình nhiều nhóm thành công'
            );
        } catch (\Exception $e) {
            $this->logError('GetMultipleGroups', $e);
            return $this->errorResponse('Không thể tải cấu hình nhiều nhóm');
        }
    }


    /**
     * Lấy cấu hình theo key
     */
    public function getConfigByKey(Request $request): JsonResponse
    {
        try {
            $key = $request->input('key');
            
            if (empty($key)) {
                return $this->successResponse([], 'Không có key nào được truyền');
            }
            
            $value = ConfigHelper::get($key);
            
            return $this->successResponse(
                ['key' => $key, 'value' => $value],
                "Lấy cấu hình key {$key} thành công"
            );
        } catch (\Exception $e) {
            $this->logError('GetConfigByKey', $e);
            return $this->errorResponse('Không thể tải cấu hình theo key');
        }
    }

    /**
     * Lấy cấu hình theo nhiều keys
     */
    public function getMultipleConfigs(Request $request): JsonResponse
    {
        try {
            $keys = $request->input('keys', []);
            
            if (empty($keys)) {
                return $this->successResponse([], 'Không có keys nào được truyền');
            }
            
            $configs = ConfigHelper::getMultiple($keys);
            
            return $this->successResponse(
                $configs,
                'Lấy cấu hình nhiều keys thành công'
            );
        } catch (\Exception $e) {
            $this->logError('GetMultipleConfigs', $e);
            return $this->errorResponse('Không thể tải cấu hình nhiều keys');
        }
    }

}
