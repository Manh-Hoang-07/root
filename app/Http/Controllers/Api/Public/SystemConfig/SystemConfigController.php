<?php

namespace App\Http\Controllers\Api\Public\SystemConfig;

use App\Http\Controllers\Api\BaseController;
use App\Services\Core\SystemConfig\SystemConfigService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SystemConfigController extends BaseController
{
    /** @var SystemConfigService */
    protected $service;
    
    /** @var bool Enable caching for responses */
    protected $enableCaching = true;
    
    /** @var int Cache TTL in seconds */
    protected $cacheTtl = 600; // 10 minutes for config data
    
    /** @var array Default fields for list view */
    protected $defaultListFields = ['key', 'value', 'type', 'group', 'description'];
    
    /** @var array Default fields for show view */
    protected $defaultShowFields = ['key', 'value', 'type', 'group', 'description', 'is_encrypted'];

    public function __construct(SystemConfigService $configService)
    {
        parent::__construct($configService);
        $this->service = $configService; // Type hint for IDE
    }

    /**
     * Get public configs by group (trả về dạng key:value)
     */
    public function getByGroup(Request $request, $group = null): JsonResponse
    {
        // Support both query parameter and path parameter
        $group = $group ?: $request->get('group');

        if (!$group) {
            return $this->apiResponse(false, null, 'Nhóm cấu hình là bắt buộc', 400);
        }

        // Always get public-only configs for public API
        $configs = $this->service->getByGroup($group, true);
        
        // Transform to key:value format
        $keyValueData = [];
        foreach ($configs as $config) {
            $keyValueData[$config['key']] = $config['value'];
        }
        
        return $this->successResponseWithFormat($keyValueData, 'Lấy cấu hình nhóm public thành công');
    }

    /**
     * Process filters to add public-only constraint
     * @param array $filters
     * @param string $context
     * @return array
     */
    protected function processFilters(array $filters, string $context = 'index'): array
    {
        // Always add public-only and active status filters
        $filters['is_public'] = true;
        $filters['status'] = 'active';
        
        return $filters;
    }

    /**
     * Get public config by key with fallback to default
     */
    public function getByKey(Request $request): JsonResponse
    {
        $key = $request->get('key');
        $default = $request->get('default');

        if (!$key) {
            return $this->apiResponse(false, null, 'Key cấu hình là bắt buộc', 400);
        }

        $value = $this->service->getByKey($key, $default, true);
        
        return $this->successResponseWithFormat([
            'key' => $key,
            'value' => $value,
            'is_default' => $value === $default
        ], 'Lấy cấu hình với giá trị mặc định thành công');
    }
}
