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
     * Clean old audit logs
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        try {
            $cutoffDate = now()->subDays($daysToKeep);
            $deletedCount = ConfigAuditLog::where('created_at', '<', $cutoffDate)->delete();
            
            return $deletedCount;
        } catch (Exception $e) {
            return 0;
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