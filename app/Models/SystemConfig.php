<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ConfigType;
use App\Enums\ConfigGroup;
use Illuminate\Support\Facades\Crypt;

class SystemConfig extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'is_encrypted',
        'validation_rules',
        'default_value',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_encrypted' => 'boolean',
        'validation_rules' => 'array',
        'sort_order' => 'integer',
        'type' => ConfigType::class,
        'group' => ConfigGroup::class,
    ];

    protected $hidden = [
        'is_encrypted',
    ];

    /**
     * Get the decrypted value
     */
    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; // Return original value if decryption fails
            }
        }
        return $value;
    }

    /**
     * Set the encrypted value
     */
    public function setValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get the typed value based on type
     */
    public function getTypedValue()
    {
        $value = $this->value;
        
        if ($value === null) {
            return $this->default_value;
        }

        return match($this->type) {
            ConfigType::INTEGER => (int) $value,
            ConfigType::FLOAT => (float) $value,
            ConfigType::BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            ConfigType::JSON => json_decode($value, true),
            ConfigType::ARRAY => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /**
     * Set the typed value based on type
     */
    public function setTypedValue($value)
    {
        $processedValue = match($this->type) {
            ConfigType::JSON, ConfigType::ARRAY => is_array($value) ? json_encode($value) : $value,
            ConfigType::BOOLEAN => $value ? '1' : '0',
            default => (string) $value,
        };

        $this->value = $processedValue;
    }

    /**
     * Scope for active configs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for public configs
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for specific group
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope for specific key
     */
    public function scopeKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Get audit logs for this config
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(ConfigAuditLog::class, 'config_key', 'key');
    }

    /**
     * Get config by key with caching
     */
    public static function getByKey($key, $default = null)
    {
        $config = static::active()->key($key)->first();
        
        if (!$config) {
            return $default;
        }

        return $config->getTypedValue();
    }

    /**
     * Set config by key
     */
    public static function setByKey($key, $value, $type = ConfigType::STRING, $group = ConfigGroup::CUSTOM)
    {
        $config = static::key($key)->first();
        
        if (!$config) {
            $config = new static([
                'key' => $key,
                'type' => $type,
                'group' => $group,
            ]);
        }

        $config->setTypedValue($value);
        $config->save();

        return $config;
    }
}
