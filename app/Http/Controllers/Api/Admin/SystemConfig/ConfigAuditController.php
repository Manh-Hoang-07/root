<?php

namespace App\Http\Controllers\Api\Admin\SystemConfig;

use App\Http\Controllers\Api\BaseController;
use App\Services\Core\SystemConfig\ConfigAuditService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ConfigAuditController extends BaseController
{
    protected $auditService;

    public function __construct(ConfigAuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Get audit logs for specific config
     */
    public function getConfigLogs(Request $request): JsonResponse
    {
        try {
            $configKey = $request->get('config_key');
            $limit = $request->get('limit', 50);

            if (!$configKey) {
                return $this->apiResponse(false, null, 'Config key là bắt buộc', 400);
            }

            $logs = $this->auditService->getConfigAuditLogs($configKey, $limit);
            return $this->apiResponse(true, $logs, 'Lấy audit logs cấu hình thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get audit logs by user
     */
    public function getUserLogs(Request $request): JsonResponse
    {
        try {
            $userId = $request->get('user_id');
            $limit = $request->get('limit', 50);

            if (!$userId) {
                return $this->apiResponse(false, null, 'User ID là bắt buộc', 400);
            }

            $logs = $this->auditService->getUserAuditLogs($userId, $limit);
            return $this->apiResponse(true, $logs, 'Lấy audit logs người dùng thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get audit logs by date range
     */
    public function getLogsByDateRange(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $limit = $request->get('limit', 100);

            if (!$startDate || !$endDate) {
                return $this->apiResponse(false, null, 'Ngày bắt đầu và kết thúc là bắt buộc', 400);
            }

            $logs = $this->auditService->getAuditLogsByDateRange($startDate, $endDate, $limit);
            return $this->apiResponse(true, $logs, 'Lấy audit logs theo ngày thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Get audit statistics
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $stats = $this->auditService->getAuditStatistics($startDate, $endDate);
            return $this->apiResponse(true, $stats, 'Lấy thống kê audit thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Clean old audit logs
     */
    public function cleanOldLogs(Request $request): JsonResponse
    {
        try {
            $daysToKeep = $request->get('days_to_keep', 90);

            $deletedCount = $this->auditService->cleanOldLogs($daysToKeep);
            return $this->apiResponse(true, ['deleted_count' => $deletedCount], "Đã xóa {$deletedCount} audit logs cũ");
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Export audit logs
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $format = $request->get('format', 'json');

            $exportData = $this->auditService->exportAuditLogs($startDate, $endDate, $format);
            
            return response($exportData)
                ->header('Content-Type', $format === 'csv' ? 'text/csv' : 'application/json')
                ->header('Content-Disposition', 'attachment; filename="audit_logs.' . $format . '"');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
