<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ConfigGroup;

class ConfigPermission extends Model
{
    protected $fillable = [
        'user_id',
        'config_group',
        'can_read',
        'can_write',
        'can_delete',
        'allowed_keys',
        'restricted_keys',
    ];

    protected $casts = [
        'can_read' => 'boolean',
        'can_write' => 'boolean',
        'can_delete' => 'boolean',
        'allowed_keys' => 'array',
        'restricted_keys' => 'array',
        'config_group' => ConfigGroup::class,
    ];

    /**
     * Get the user that owns the permission
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user can access specific key
     */
    public function canAccessKey($key): bool
    {
        // Check if key is in restricted list
        if ($this->restricted_keys && in_array($key, $this->restricted_keys)) {
            return false;
        }

        // Check if key is in allowed list (if specified)
        if ($this->allowed_keys && !in_array($key, $this->allowed_keys)) {
            return false;
        }

        return true;
    }

    /**
     * Check if user has read permission
     */
    public function canRead(): bool
    {
        return $this->can_read;
    }

    /**
     * Check if user has write permission
     */
    public function canWrite(): bool
    {
        return $this->can_write;
    }

    /**
     * Check if user has delete permission
     */
    public function canDelete(): bool
    {
        return $this->can_delete;
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific group
     */
    public function scopeForGroup($query, $group)
    {
        return $query->where('config_group', $group);
    }

    /**
     * Scope for read permissions
     */
    public function scopeCanRead($query)
    {
        return $query->where('can_read', true);
    }

    /**
     * Scope for write permissions
     */
    public function scopeCanWrite($query)
    {
        return $query->where('can_write', true);
    }
}
