<?php

namespace App\Http\Controllers\Api\Admin\SystemConfig;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\SystemConfig\SystemConfigRequest;
use App\Services\Admin\SystemConfig\SystemConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemConfigController extends BaseController
{
    protected $storeRequestClass = SystemConfigRequest::class;
    protected $updateRequestClass = SystemConfigRequest::class;

    public function __construct(SystemConfigService $service)
    {
        parent::__construct($service);
    }

    /**
     * Get configs by group (Admin)
     */
    public function getByGroup(string $group): JsonResponse
    {
        try {
            $data = $this->service->getByGroup($group);
            return $this->apiResponse(true, $this->formatSystemConfigData($data), "Lấy cấu hình nhóm {$group} thành công");
        } catch (\Exception $e) {
            $this->logError('GetByGroup', $e);
            return $this->apiResponse(false, null, 'Không thể tải cấu hình nhóm', 500);
        }
    }

    /**
     * Update configs by group (Admin)
     */
    public function updateGroup(Request $request, string $group): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'configs' => 'required|array'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(false, null, 'Dữ liệu không hợp lệ', 422, $validator->errors()->toArray());
            }

            $configs = $request->input('configs', []);
            $configData = [];
            
            foreach ($configs as $key => $value) {
                $configData[$key] = $value;
            }

            $result = $this->service->updateGroup($group, $configData);
            
            if ($result) {
                return $this->apiResponse(true, null, "Cập nhật cấu hình nhóm {$group} thành công");
            }
            
            return $this->apiResponse(false, null, 'Không thể cập nhật cấu hình nhóm', 500);
        } catch (\Exception $e) {
            $this->logError('UpdateGroup', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật cấu hình nhóm', 500);
        }
    }

    /**
     * Get group form structure (Admin)
     */
    public function getGroupForm(string $group): JsonResponse
    {
        try {
            $configs = $this->service->getByGroup($group);
            
            $formData = [];
            foreach ($configs as $config) {
                $formData[] = [
                    'id' => $config->id,
                    'config_key' => $config->config_key,
                    'display_name' => $config->display_name,
                    'description' => $config->description,
                    'config_type' => $config->config_type,
                    'config_value' => $config->config_value,
                    'value' => $config->value,
                    'is_required' => $config->is_required,
                    'validation_rules' => $config->validation_rules,
                    'sort_order' => $config->sort_order
                ];
            }

            return $this->apiResponse(true, $formData, "Lấy form cấu hình nhóm {$group} thành công");
        } catch (\Exception $e) {
            $this->logError('GetGroupForm', $e);
            return $this->apiResponse(false, null, 'Không thể tải form cấu hình nhóm', 500);
        }
    }

    /**
     * Bulk update configs (Admin)
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'configs' => 'required|array',
                'configs.*.id' => 'required|integer|exists:system_configs,id',
                'configs.*.config_value' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(false, null,'Dữ liệu không hợp lệ', 422, $validator->errors()->toArray());
            }

            $configs = $request->input('configs', []);
            $updated = $this->service->bulkUpdate($configs);
            
            return $this->apiResponse(true,
                ['updated_count' => $updated],
                "Cập nhật hàng loạt {$updated} cấu hình thành công"
            );
        } catch (\Exception $e) {
            $this->logError('BulkUpdate', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật hàng loạt cấu hình', 500);
        }
    }

    /**
     * Bulk delete configs (Admin)
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:system_configs,id'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse(false, null,'Dữ liệu không hợp lệ', 422, $validator->errors()->toArray());
            }

            $ids = $request->input('ids', []);
            $deleted = $this->service->bulkDelete($ids);
            
            return $this->apiResponse(true,
                ['deleted_count' => $deleted],
                "Xóa hàng loạt {$deleted} cấu hình thành công"
            );
        } catch (\Exception $e) {
            $this->logError('BulkDelete', $e);
            return $this->apiResponse(false, null, 'Không thể xóa hàng loạt cấu hình', 500);
        }
    }

    /**
     * Get groups list (Admin)
     */
    public function getGroups(): JsonResponse
    {
        try {
            $groups = $this->service->getGroups();
            return $this->apiResponse(true, $groups, 'Lấy danh sách nhóm cấu hình thành công');
        } catch (\Exception $e) {
            $this->logError('GetGroups', $e);
            return $this->apiResponse(false, null, 'Không thể tải danh sách nhóm cấu hình', 500);
        }
    }

    /**
     * Search configs (Admin)
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $data = $this->service->search($filters);
            return $this->apiResponse(true, $this->formatSystemConfigData($data), 'Tìm kiếm cấu hình thành công');
        } catch (\Exception $e) {
            $this->logError('Search', $e);
            return $this->apiResponse(false, null, 'Không thể tìm kiếm cấu hình', 500);
        }
    }

    /**
     * Clear cache (Admin)
     */
    public function clearCache(): JsonResponse
    {
        try {
            $result = $this->service->clearCache();
            return $this->apiResponse(true, null, 'Xóa cache thành công');
        } catch (\Exception $e) {
            $this->logError('ClearCache', $e);
            return $this->apiResponse(false, null, 'Không thể xóa cache', 500);
        }
    }

    /**
     * Config V2 Admin routes
     */
    public function updateGeneralConfig(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $result = $this->service->updateGroup('general', $data);
            
            return $this->apiResponse(true, $result, 'Cập nhật cấu hình hệ thống thành công');
        } catch (\Exception $e) {
            $this->logError('UpdateGeneralConfig', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật cấu hình hệ thống', 500);
        }
    }

    public function updateEmailConfig(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $result = $this->service->updateGroup('email', $data);
            
            return $this->apiResponse(true, $result, 'Cập nhật cấu hình email thành công');
        } catch (\Exception $e) {
            $this->logError('UpdateEmailConfig', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật cấu hình email', 500);
        }
    }

    public function updateByKey(Request $request): JsonResponse
    {
        try {
            $key = $request->input('key');
            $value = $request->input('value');
            
            if (empty($key)) {
                return $this->apiResponse(false, null, 'Key là bắt buộc', 400);
            }
            
            $result = $this->service->updateByKeyResponse($key, $value);
            
            return $result;
        } catch (\Exception $e) {
            $this->logError('UpdateByKey', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật cấu hình theo key', 500);
        }
    }

    /**
     * Format SystemConfig data giống như SystemConfigResource
     */
    private function formatSystemConfigData($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'formatSystemConfigData'], $data);
        }
        
        if (is_object($data) && method_exists($data, 'toArray')) {
            $dataArray = $data->toArray();
        } else {
            $dataArray = (array) $data;
        }
        
        return [
            'id' => $dataArray['id'] ?? null,
            'config_key' => $dataArray['config_key'] ?? null,
            'config_value' => $dataArray['config_value'] ?? null,
            'value' => $dataArray['value'] ?? null, // Computed value with type casting
            'config_type' => $dataArray['config_type'] ?? null,
            'config_group' => $dataArray['config_group'] ?? null,
            'display_name' => $dataArray['display_name'] ?? null,
            'description' => $dataArray['description'] ?? null,
            'is_public' => $dataArray['is_public'] ?? null,
            'is_required' => $dataArray['is_required'] ?? null,
            'validation_rules' => $dataArray['validation_rules'] ?? null,
            'default_value' => $dataArray['default_value'] ?? null,
            'sort_order' => $dataArray['sort_order'] ?? null,
            'status' => $dataArray['status'] ?? null,
            'created_at' => isset($dataArray['created_at']) ? 
                (is_string($dataArray['created_at']) ? $dataArray['created_at'] : $dataArray['created_at']->format('Y-m-d H:i:s')) : null,
            'updated_at' => isset($dataArray['updated_at']) ? 
                (is_string($dataArray['updated_at']) ? $dataArray['updated_at'] : $dataArray['updated_at']->format('Y-m-d H:i:s')) : null,
        ];
    }
}
