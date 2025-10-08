<?php

namespace App\Services\Core\SystemConfig;

use App\Models\ConfigAuditLog;
use App\Models\SystemConfig;
use App\Enums\ConfigAction;
use Illuminate\Support\Facades\Request;
use Exception;

class ConfigAuditService
{
    /**
     * Log config change
     */
    public function logConfigChange(
        string $configKey,
        ?string $oldValue,
        ?string $newValue,
        string $action,
        ?int $userId = null
    ): bool {
        try {
            $auditData = [
                'config_key' => $configKey,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'action' => $action,
                'changed_by' => $userId,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'metadata' => $this->getMetadata($configKey, $oldValue, $newValue),
            ];

            ConfigAuditLog::create($auditData);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get audit logs for specific config
     */
    public function getConfigAuditLogs(string $configKey, int $limit = 50): array
    {
        try {
            $logs = ConfigAuditLog::where('config_key', $configKey)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

            return $logs;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get audit logs by user
     */
    public function getUserAuditLogs(int $userId, int $limit = 50): array
    {
        try {
            $logs = ConfigAuditLog::where('changed_by', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

            return $logs;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get audit logs by date range
     */
    public function getAuditLogsByDateRange(string $startDate, string $endDate, int $limit = 100): array
    {
        try {
            $logs = ConfigAuditLog::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

            return $logs;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get audit statistics
     */
    public function getAuditStatistics(?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $query = ConfigAuditLog::query();
            
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $total = $query->count();
            $byAction = $query->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray();
            
            $byUser = $query->selectRaw('changed_by, COUNT(*) as count')
                ->whereNotNull('changed_by')
                ->groupBy('changed_by')
                ->pluck('count', 'changed_by')
                ->toArray();

            return [
                'total' => $total,
                'by_action' => $byAction,
                'by_user' => $byUser,
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate
                ]
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'by_action' => [],
                'by_user' => [],
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate
                ]
            ];
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