<?php

namespace App\Http\Controllers\Api\Admin\SystemConfig;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\SystemConfig\SystemConfigRequest;
use App\Http\Resources\SystemConfig\SystemConfigResource;
use App\Services\SystemConfig\SystemConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
     * Get configs by group
     */
    public function getByGroup(string $group): JsonResponse
    {
        try {
            $data = $this->service->getByGroup($group);
            
            return $this->successResponse(
                SystemConfigResource::collection($data),
                "Lấy cấu hình nhóm {$group} thành công"
            );
        } catch (\Exception $e) {
            $this->logError('GetByGroup', $e);
            return $this->errorResponse('Không thể tải cấu hình nhóm');
        }
    }

    /**
     * Update configs by group
     */
    public function updateGroup(Request $request, string $group): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'configs' => 'required|array'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Dữ liệu không hợp lệ', 422, $validator->errors());
            }

            $configs = $request->input('configs', []);
            $configData = [];
            
            // Xử lý dữ liệu dạng key-value
            foreach ($configs as $key => $value) {
                $configData[$key] = $value;
            }

            $result = $this->service->updateGroup($group, $configData);
            
            if ($result) {
                return $this->successResponse(
                    null,
                    "Cập nhật cấu hình nhóm {$group} thành công"
                );
            }
            
            return $this->errorResponse('Không thể cập nhật cấu hình nhóm');
        } catch (\Exception $e) {
            $this->logError('UpdateGroup', $e);
            return $this->errorResponse('Không thể cập nhật cấu hình nhóm');
        }
    }

    /**
     * Get group form structure
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

            return $this->successResponse(
                $formData,
                "Lấy form cấu hình nhóm {$group} thành công"
            );
        } catch (\Exception $e) {
            $this->logError('GetGroupForm', $e);
            return $this->errorResponse('Không thể tải form cấu hình nhóm');
        }
    }

    /**
     * Bulk update configs
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
                return $this->errorResponse('Dữ liệu không hợp lệ', 422, $validator->errors());
            }

            $configs = $request->input('configs', []);
            $updated = $this->service->bulkUpdate($configs);
            
            return $this->successResponse(
                ['updated_count' => $updated],
                "Cập nhật hàng loạt {$updated} cấu hình thành công"
            );
        } catch (\Exception $e) {
            $this->logError('BulkUpdate', $e);
            return $this->errorResponse('Không thể cập nhật hàng loạt cấu hình');
        }
    }

    /**
     * Bulk delete configs
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:system_configs,id'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Dữ liệu không hợp lệ', 422, $validator->errors());
            }

            $ids = $request->input('ids', []);
            $deleted = $this->service->bulkDelete($ids);
            
            return $this->successResponse(
                ['deleted_count' => $deleted],
                "Xóa hàng loạt {$deleted} cấu hình thành công"
            );
        } catch (\Exception $e) {
            $this->logError('BulkDelete', $e);
            return $this->errorResponse('Không thể xóa hàng loạt cấu hình');
        }
    }

    /**
     * Clear cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::flush();
            
            return $this->successResponse(
                null,
                'Xóa cache thành công'
            );
        } catch (\Exception $e) {
            $this->logError('ClearCache', $e);
            return $this->errorResponse('Không thể xóa cache');
        }
    }

    /**
     * Get groups list
     */
    public function getGroups(): JsonResponse
    {
        try {
            $groups = $this->service->getGroups();
            
            return $this->successResponse(
                $groups,
                'Lấy danh sách nhóm cấu hình thành công'
            );
        } catch (\Exception $e) {
            $this->logError('GetGroups', $e);
            return $this->errorResponse('Không thể tải danh sách nhóm cấu hình');
        }
    }

    /**
     * Search configs
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $data = $this->service->search($filters);
            
            return $this->successResponse(
                SystemConfigResource::collection($data),
                'Tìm kiếm cấu hình thành công'
            );
        } catch (\Exception $e) {
            $this->logError('Search', $e);
            return $this->errorResponse('Không thể tìm kiếm cấu hình');
        }
    }
}