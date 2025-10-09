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
    protected $storeRequestClass = SystemConfigRequest::class;
    protected $updateRequestClass = SystemConfigRequest::class;
    protected $indexRelations = [];
    protected $showRelations = [];
    protected $defaultPerPage = 15;
    protected $maxPerPage = 100;
    
    /** @var SystemConfigService */
    protected $service;

    public function __construct(SystemConfigService $service)
    {
        parent::__construct($service);
    }

    /**
     * Get all config groups
     */
    public function getGroups(): JsonResponse
    {
        try {
            $groups = $this->service->getGroups();
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

            $configs = $this->service->getByGroup($group, $publicOnly);
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

            $value = $this->service->getByKey($key, $default, $publicOnly);
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

            $configs = $this->service->getByKeys($keys, $publicOnly);
            return $this->apiResponse(true, $configs, 'Lấy cấu hình thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Override BaseController store to handle createOrUpdate logic
     */
    public function store(): JsonResponse
    {
        try {
            $request = app($this->getStoreRequestClass());
            $data = $request->validated();
            $userId = $request->user()?->id;

            // Sử dụng key làm condition để createOrUpdate
            $result = $this->service->createOrUpdate(['key' => $data['key']], $data, $userId);
            
            return $this->apiResponse(true, $result, 'Tạo/cập nhật cấu hình thành công', 201);
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

            $result = $this->service->bulkUpdate($configs, $userId);
            
            if ($result['success']) {
                return $this->apiResponse(true, $result['data'], $result['message']);
            }

            return $this->apiResponse(false, null, $result['message'], 422);
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
            $result = $this->service->clearAllCache();
            
            if ($result) {
                return $this->apiResponse(true, null, 'Xóa cache thành công');
            }

            return $this->apiResponse(false, null, 'Không thể xóa cache', 500);
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
