<?php

namespace App\Services\Core\SystemConfig;

use App\Repositories\ConfigAudit\ConfigAuditRepository;
use App\Services\BaseService;
use App\Models\ConfigAuditLog;
use App\Models\SystemConfig;
use App\Enums\ConfigAction;
use Illuminate\Support\Facades\Request;
use Exception;

class ConfigAuditService extends BaseService
{
    /**
     * @var ConfigAuditRepository
     */
    protected $repo;

    public function __construct(ConfigAuditRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create audit log
     */
    public function create($data): array
    {
        try {
            $auditData = [
                'config_key' => $data['config_key'],
                'old_value' => $data['old_value'] ?? null,
                'new_value' => $data['new_value'] ?? null,
                'action' => $data['action'],
                'changed_by' => $data['changed_by'] ?? null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'metadata' => $this->getMetadata($data['config_key'], $data['old_value'] ?? null, $data['new_value'] ?? null),
            ];

            return parent::create($auditData);
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * Get audit logs with flexible filtering
     */
    public function getAuditLogs(array $filters = [], int $perPage = 50): array
    {
        try {
            $conditions = [];
            
            // Filter by config key
            if (!empty($filters['config_key'])) {
                $conditions['config_key'] = $filters['config_key'];
            }
            
            // Filter by user ID
            if (!empty($filters['user_id'])) {
                $conditions['changed_by'] = $filters['user_id'];
            }
            
            // Filter by action
            if (!empty($filters['action'])) {
                $conditions['action'] = $filters['action'];
            }
            
            // Filter by date range
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $conditions[] = ['created_at', '>=', $filters['start_date']];
                $conditions[] = ['created_at', '<=', $filters['end_date']];
            }
            
            return $this->list($conditions, $perPage);
        } catch (Exception $e) {
            throw new Exception("Failed to get audit logs: " . $e->getMessage());
        }
    }

    /**
     * Export audit logs
     */
    public function exportAuditLogs(?string $startDate = null, ?string $endDate = null, string $format = 'json'): string
    {
        try {
            $query = ConfigAuditLog::query();
            
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $logs = $query->orderBy('created_at', 'desc')->get()->toArray();

            if ($format === 'csv') {
                return $this->exportToCsv($logs);
            }

            return json_encode($logs, JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get metadata for audit log
     */
    private function getMetadata(string $configKey, ?string $oldValue, ?string $newValue): array
    {
        return [
            'config_key' => $configKey,
            'value_changed' => $oldValue !== $newValue,
            'timestamp' => now()->toISOString(),
            'request_id' => request()->header('X-Request-ID'),
        ];
    }

    /**
     * Export logs to CSV format
     */
    private function exportToCsv(array $logs): string
    {
        if (empty($logs)) {
            return '';
        }

        $headers = array_keys($logs[0]);
        $csv = implode(',', $headers) . "\n";

        foreach ($logs as $log) {
            $row = array_map(function($value) {
                return is_array($value) ? json_encode($value) : $value;
            }, $log);
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }
}