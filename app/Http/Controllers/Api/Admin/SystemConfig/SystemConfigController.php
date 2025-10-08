<?php

namespace App\Http\Controllers\Api\Admin\SystemConfig;

use App\Http\Controllers\Api\BaseController;
use App\Services\Core\SystemConfig\SystemConfigService;
use App\Http\Requests\Core\SystemConfig\SystemConfigRequest;
use App\Http\Requests\Core\SystemConfig\BulkUpdateConfigRequest;
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
     * Get all config groups
     */
    public function getGroups(): JsonResponse
    {
        try {
            $groups = $this->configService->getGroups();
            return $this->apiResponse(true, $groups, 'Lấy danh sách nhóm cấu hình thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get configs by group
     */
    public function getByGroup(Request $request): JsonResponse
    {
        try {
            $group = $request->get('group');
            $publicOnly = $request->boolean('public_only', false);

            if (!$group) {
                return $this->apiResponse(false, null, 'Nhóm cấu hình là bắt buộc', 400);
            }

            $configs = $this->configService->getByGroup($group, $publicOnly);
            return $this->apiResponse(true, $configs, 'Lấy cấu hình nhóm thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get config by key
     */
    public function getByKey(Request $request): JsonResponse
    {
        try {
            $key = $request->get('key');
            $default = $request->get('default');
            $publicOnly = $request->boolean('public_only', false);

            if (!$key) {
                return $this->apiResponse(false, null, 'Key cấu hình là bắt buộc', 400);
            }

            $value = $this->configService->getByKey($key, $default, $publicOnly);
            return $this->apiResponse(true, ['key' => $key, 'value' => $value], 'Lấy cấu hình thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get multiple configs by keys
     */
    public function getByKeys(Request $request): JsonResponse
    {
        try {
            $keys = $request->get('keys', []);
            $publicOnly = $request->boolean('public_only', false);

            if (empty($keys) || !is_array($keys)) {
                return $this->apiResponse(false, null, 'Danh sách keys là bắt buộc', 400);
            }

            $configs = $this->configService->getByKeys($keys, $publicOnly);
            return $this->apiResponse(true, $configs, 'Lấy cấu hình thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Create or update config
     */
    public function storeConfig(SystemConfigRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $userId = $request->user()?->id;

            $result = $this->configService->createOrUpdate($data, $userId);
            
            if ($result['success']) {
                return $this->apiResponse(true, $result['data'], $result['message'], 201);
            }

            return $this->apiResponse(false, null, $result['message'], 422);
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Bulk update configs
     */
    public function bulkUpdate(BulkUpdateConfigRequest $request): JsonResponse
    {
        try {
            $configs = $request->validated()['configs'];
            $userId = $request->user()?->id;

            $result = $this->configService->bulkUpdate($configs, $userId);
            
            if ($result['success']) {
                return $this->apiResponse(true, $result['data'], $result['message']);
            }

            return $this->apiResponse(false, null, $result['message'], 422);
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Delete config
     */
    public function destroyConfig(Request $request): JsonResponse
    {
        try {
            $key = $request->get('key');

            if (!$key) {
                return $this->apiResponse(false, null, 'Key cấu hình là bắt buộc', 400);
            }

            $userId = $request->user()?->id;
            $result = $this->configService->deleteByKey($key, $userId);
            
            if ($result['success']) {
                return $this->apiResponse(true, null, $result['message']);
            }

            return $this->apiResponse(false, null, $result['message'], 422);
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get configs with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only(['group', 'is_public', 'is_active', 'search', 'type']);

            $configs = $this->configService->list($filters, $perPage);
            return $this->apiResponse(true, $configs, 'Lấy danh sách cấu hình thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Search configs
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search');
            $filters = $request->only(['group', 'type', 'is_public']);

            if (!$search) {
                return $this->apiResponse(false, null, 'Từ khóa tìm kiếm là bắt buộc', 400);
            }

            $filters['search'] = $search;
            $configs = $this->configService->list($filters, 50);
            return $this->apiResponse(true, $configs, 'Tìm kiếm cấu hình thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }


    /**
     * Clear all config cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $result = $this->configService->clearAllCache();
            
            if ($result) {
                return $this->apiResponse(true, null, 'Xóa cache thành công');
            }

            return $this->apiResponse(false, null, 'Không thể xóa cache', 500);
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get configs for specific user
     */
    public function getForUser(Request $request): JsonResponse
    {
        try {
            $userId = $request->get('user_id');
            $group = $request->get('group');

            if (!$userId) {
                return $this->apiResponse(false, null, 'User ID là bắt buộc', 400);
            }

            $conditions = ['status' => 'active'];
            if ($group) {
                $conditions['group'] = $group;
            }
            $configs = $this->configService->getBy($conditions);
            return $this->apiResponse(true, $configs, 'Lấy cấu hình người dùng thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
