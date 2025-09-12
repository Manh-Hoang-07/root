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
            
            return $this->apiResponse(true, $configs, 'Lấy cấu hình public thành công');
        } catch (\Exception $e) {
            $this->logError('GetPublicConfigs', $e);
            return $this->apiResponse(false, null, 'Không thể tải cấu hình public', 500);
        }
    }

    /**
     * Lấy cấu hình theo group
     */
    public function getGroupConfig(string $group): JsonResponse
    {
        try {
            $configs = ConfigHelper::getGroup($group);
            
            return $this->apiResponse(true, $configs, "Lấy cấu hình nhóm {$group} thành công");
        } catch (\Exception $e) {
            $this->logError('GetGroupConfig', $e);
            return $this->apiResponse(false, null, "Không thể tải cấu hình nhóm {$group}", 500);
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
                return $this->apiResponse(true, [], 'Không có groups nào được truyền');
            }
            
            $configs = ConfigHelper::getMultipleGroups($groups);
            
            return $this->apiResponse(true, $configs, 'Lấy cấu hình nhiều nhóm thành công');
        } catch (\Exception $e) {
            $this->logError('GetMultipleGroups', $e);
            return $this->apiResponse(false, null, 'Không thể tải cấu hình nhiều nhóm', 500);
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
                return $this->apiResponse(true, [], 'Không có key nào được truyền');
            }
            
            $value = ConfigHelper::get($key);
            
            return $this->apiResponse(true, ['key' => $key, 'value' => $value], "Lấy cấu hình key {$key} thành công");
        } catch (\Exception $e) {
            $this->logError('GetConfigByKey', $e);
            return $this->apiResponse(false, null, 'Không thể tải cấu hình theo key', 500);
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
                return $this->apiResponse(true, [], 'Không có keys nào được truyền');
            }
            
            $configs = ConfigHelper::getMultiple($keys);
            
            return $this->apiResponse(true, $configs, 'Lấy cấu hình nhiều keys thành công');
        } catch (\Exception $e) {
            $this->logError('GetMultipleConfigs', $e);
            return $this->apiResponse(false, null, 'Không thể tải cấu hình nhiều keys', 500);
        }
    }

}
