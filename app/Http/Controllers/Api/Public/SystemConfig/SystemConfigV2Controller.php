<?php

namespace App\Http\Controllers\Api\Public\SystemConfig;

use App\Http\Controllers\Api\BaseController;
use App\Services\Public\SystemConfig\SystemConfigService;
use App\Helpers\ConfigHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemConfigV2Controller extends BaseController
{
    protected SystemConfigService $configService;

    public function __construct(SystemConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Lấy tất cả cấu hình public
     */
    public function getPublicConfigs(): JsonResponse
    {
        try {
            $configs = $this->configService->getGeneralConfig();
            
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

    /**
     * Lấy cấu hình chung (cho Config V2 API)
     */
    public function getGeneralConfig(): JsonResponse
    {
        try {
            $configs = $this->configService->getGeneralConfig();
            
            return $this->apiResponse(true, $configs, 'Lấy cấu hình chung thành công');
        } catch (\Exception $e) {
            $this->logError('GetGeneralConfig', $e);
            return $this->apiResponse(false, null, 'Không thể tải cấu hình chung', 500);
        }
    }

    /**
     * Lấy cấu hình email (cho Config V2 API)
     */
    public function getEmailConfig(): JsonResponse
    {
        try {
            $configs = ConfigHelper::getGroup('email');
            
            return $this->apiResponse(true, $configs, 'Lấy cấu hình email thành công');
        } catch (\Exception $e) {
            $this->logError('GetEmailConfig', $e);
            return $this->apiResponse(false, null, 'Không thể tải cấu hình email', 500);
        }
    }

    /**
     * Lấy cấu hình theo key (cho Config V2 API)
     */
    public function getByKey(Request $request): JsonResponse
    {
        try {
            $key = $request->input('key');
            $default = $request->input('default');
            
            if (empty($key)) {
                return $this->apiResponse(false, null, 'Key là bắt buộc', 400);
            }
            
            $value = ConfigHelper::get($key, $default);
            
            return $this->apiResponse(true, ['key' => $key, 'value' => $value], "Lấy cấu hình key {$key} thành công");
        } catch (\Exception $e) {
            $this->logError('GetByKey', $e);
            return $this->apiResponse(false, null, 'Không thể tải cấu hình theo key', 500);
        }
    }

}
