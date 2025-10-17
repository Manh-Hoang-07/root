<?php

namespace App\Http\Controllers\Api\Admin\SystemConfig;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Core\SystemConfig\ConfigAuditService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigAuditController extends CrudController
{
    protected $indexRelations = [];
    protected $showRelations = [];
    protected $defaultPerPage = 50;
    protected $maxPerPage = 200;

    /** @var ConfigAuditService */
    protected $service;

    public function __construct(ConfigAuditService $service)
    {
        parent::__construct($service);
    }

    /**
     * Get audit logs with flexible filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['config_key', 'user_id', 'start_date', 'end_date', 'action']);
            $perPage = $request->get('per_page', $this->defaultPerPage);
            $perPage = min($perPage, $this->maxPerPage);

            $logs = $this->service->getAuditLogs($filters, $perPage);
            return $this->successResponseWithFormat($logs, 'Lấy audit logs thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $format = $request->get('format', 'json');

            $exportData = $this->service->exportAuditLogs($startDate, $endDate, $format);

            return response($exportData)
                ->header('Content-Type', $format === 'csv' ? 'text/csv' : 'application/json')
                ->header('Content-Disposition', 'attachment; filename="audit_logs.' . $format . '"');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
