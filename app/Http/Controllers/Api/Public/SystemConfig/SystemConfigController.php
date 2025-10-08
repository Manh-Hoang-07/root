<?php

namespace App\Http\Controllers\Api\Public\SystemConfig;

use App\Http\Controllers\Api\BaseController;
use App\Services\Core\SystemConfig\SystemConfigService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class SystemConfigController extends BaseController
{
    protected $configService;

    public function __construct(SystemConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Get all public config groups
     */
    public function getGroups(): JsonResponse
    {
        try {
            $groups = collect($this->configService->getGroups())
                ->filter(function ($group) {
                    return $group['is_public'];
                })
                ->values()
                ->toArray();

            return $this->apiResponse(true, $groups, 'Lấy danh sách nhóm cấu hình public thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get public configs by group
     */
    public function getByGroup(Request $request): JsonResponse
    {
        try {
            $group = $request->get('group');

            if (!$group) {
                return $this->apiResponse(false, null, 'Nhóm cấu hình là bắt buộc', 400);
            }

            $configs = $this->configService->getByGroup($group, true);
            return $this->apiResponse(true, $configs, 'Lấy cấu hình nhóm public thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get public config by key
     */
    public function getByKey(Request $request): JsonResponse
    {
        try {
            $key = $request->get('key');
            $default = $request->get('default');

            if (!$key) {
                return $this->apiResponse(false, null, 'Key cấu hình là bắt buộc', 400);
            }

            $value = $this->configService->getByKey($key, $default, true);
            return $this->apiResponse(true, ['key' => $key, 'value' => $value], 'Lấy cấu hình public thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get multiple public configs by keys
     */
    public function getByKeys(Request $request): JsonResponse
    {
        try {
            $keys = $request->get('keys', []);

            if (empty($keys) || !is_array($keys)) {
                return $this->apiResponse(false, null, 'Danh sách keys là bắt buộc', 400);
            }

            $configs = $this->configService->getByKeys($keys, true);
            return $this->apiResponse(true, $configs, 'Lấy cấu hình public thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get all public configs
     */
    public function getAll(): JsonResponse
    {
        try {
            $configs = $this->configService->getPublicConfigs();
            return $this->apiResponse(true, $configs, 'Lấy tất cả cấu hình public thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Search public configs
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search');
            $filters = $request->only(['group', 'type']);
            $filters['is_public'] = true; // Only public configs

            if (!$search) {
                return $this->apiResponse(false, null, 'Từ khóa tìm kiếm là bắt buộc', 400);
            }

            $configs = $this->configService->search($search, $filters);
            return $this->apiResponse(true, $configs, 'Tìm kiếm cấu hình public thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get config by key with fallback to default
     */
    public function getWithDefault(Request $request): JsonResponse
    {
        try {
            $key = $request->get('key');
            $default = $request->get('default');

            if (!$key) {
                return $this->apiResponse(false, null, 'Key cấu hình là bắt buộc', 400);
            }

            $value = $this->configService->getByKey($key, $default, true);
            
            return $this->apiResponse(true, [
                'key' => $key,
                'value' => $value,
                'is_default' => $value === $default
            ], 'Lấy cấu hình với giá trị mặc định thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get configs by multiple groups
     */
    public function getByGroups(Request $request): JsonResponse
    {
        try {
            $groups = $request->get('groups', []);

            if (empty($groups) || !is_array($groups)) {
                return $this->apiResponse(false, null, 'Danh sách nhóm cấu hình là bắt buộc', 400);
            }

            $allConfigs = [];
            foreach ($groups as $group) {
                $configs = $this->configService->getByGroup($group, true);
                $allConfigs[$group] = $configs;
            }

            return $this->apiResponse(true, $allConfigs, 'Lấy cấu hình theo nhiều nhóm thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
