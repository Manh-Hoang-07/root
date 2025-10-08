<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ConfigAction;

class ConfigAuditLog extends Model
{
    protected $fillable = [
        'config_key',
        'old_value',
        'new_value',
        'action',
        'changed_by',
        'change_reason',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'action' => ConfigAction::class,
    ];

    public $timestamps = false;

    protected $dates = ['created_at'];

    /**
     * Get the user who made the change
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get the config that was changed
     */
    public function config(): BelongsTo
    {
        return $this->belongsTo(SystemConfig::class, 'config_key', 'key');
    }

    /**
     * Scope for specific config key
     */
    public function scopeForConfig($query, $key)
    {
        return $query->where('config_key', $key);
    }

    /**
     * Scope for specific action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted change description
     */
    public function getChangeDescriptionAttribute(): string
    {
        $actionLabel = $this->action->getLabel();
        $configKey = $this->config_key;
        
        return match($this->action) {
            ConfigAction::CREATED => "Tạo mới cấu hình '{$configKey}'",
            ConfigAction::UPDATED => "Cập nhật cấu hình '{$configKey}'",
            ConfigAction::DELETED => "Xóa cấu hình '{$configKey}'",
            ConfigAction::RESTORED => "Khôi phục cấu hình '{$configKey}'",
        };
    }

    /**
     * Get value change summary
     */
    public function getValueChangeSummaryAttribute(): string
    {
        if ($this->action === ConfigAction::CREATED) {
            return "Giá trị mới: " . ($this->new_value ?? 'null');
        }

        if ($this->action === ConfigAction::DELETED) {
            return "Giá trị cũ: " . ($this->old_value ?? 'null');
        }

        if ($this->action === ConfigAction::UPDATED) {
            return "Từ: " . ($this->old_value ?? 'null') . " → " . ($this->new_value ?? 'null');
        }

        return '';
    }
}
