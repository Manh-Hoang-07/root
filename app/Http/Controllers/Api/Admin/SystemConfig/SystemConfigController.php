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
    /** @var SystemConfigService */
    protected $service;
    
    protected $storeRequestClass = SystemConfigRequest::class;
    protected $updateRequestClass = SystemConfigRequest::class;
    protected $indexRelations = [];
    protected $showRelations = [];
    protected $defaultPerPage = 15;
    protected $maxPerPage = 100;
    
    /** @var bool Enable caching for responses */
    protected $enableCaching = true;
    
    /** @var int Cache TTL in seconds */
    protected $cacheTtl = 300; // 5 minutes for admin config data
    
    /** @var array Default fields for list view */
    protected $defaultListFields = ['id', 'key', 'value', 'type', 'group', 'description', 'is_public', 'is_encrypted', 'status'];
    
    /** @var array Default fields for show view */
    protected $defaultShowFields = ['id', 'key', 'value', 'type', 'group', 'description', 'is_public', 'is_encrypted', 'status', 'validation_rules', 'default_value', 'sort_order'];

    public function __construct(SystemConfigService $service)
    {
        parent::__construct($service);
        $this->service = $service; // Type hint for IDE
    }


    /**
     * Get configs by group
     */
    public function getByGroup(Request $request): JsonResponse
    {
        $group = $request->get('group');
        $publicOnly = $request->boolean('public_only', false);

        if (!$group) {
            return $this->apiResponse(false, null, 'Nhóm cấu hình là bắt buộc', 400);
        }

        $configs = $this->service->getByGroup($group, $publicOnly);
        return $this->successResponseWithFormat($configs, 'Lấy cấu hình nhóm thành công');
    }

    /**
     * Get config by key
     */
    public function getByKey(Request $request): JsonResponse
    {
        $key = $request->get('key');
        $default = $request->get('default');
        $publicOnly = $request->boolean('public_only', false);

        if (!$key) {
            return $this->apiResponse(false, null, 'Key cấu hình là bắt buộc', 400);
        }

        $value = $this->service->getByKey($key, $default, $publicOnly);
        return $this->successResponseWithFormat(['key' => $key, 'value' => $value], 'Lấy cấu hình thành công');
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
