<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SystemConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'config_key',
        'config_value',
        'config_type',
        'config_group',
        'display_name',
        'description',
        'is_public',
        'is_required',
        'validation_rules',
        'default_value',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_required' => 'boolean',
        'validation_rules' => 'array',
        'sort_order' => 'integer'
    ];

    /**
     * Scope để lấy config theo group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('config_group', $group);
    }

    /**
     * Scope để lấy config active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope để lấy config public
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope để sắp xếp theo sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    /**
     * Lấy giá trị config với type casting
     */
    public function getValueAttribute()
    {
        $value = $this->config_value ?? $this->default_value;

        switch ($this->config_type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? (float) $value : 0;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Set giá trị config với type casting
     */
    public function setValueAttribute($value)
    {
        switch ($this->config_type) {
            case 'boolean':
                $this->config_value = $value ? 'true' : 'false';
                break;
            case 'number':
                $this->config_value = (string) $value;
                break;
            case 'json':
                $this->config_value = is_array($value) ? json_encode($value) : $value;
                break;
            default:
                $this->config_value = (string) $value;
        }
    }

    /**
     * Lấy config theo key
     */
    public static function getByKey($key, $default = null)
    {
        $config = static::where('config_key', $key)->active()->first();
        return $config ? $config->value : $default;
    }

    /**
     * Lấy tất cả config theo group
     */
    public static function getByGroup($group)
    {
        return static::byGroup($group)->active()->ordered()->get();
    }

    /**
     * Lấy config theo group dạng key-value
     */
    public static function getGroupAsArray($group)
    {
        $configs = static::getByGroup($group);
        $result = [];
        
        foreach ($configs as $config) {
            $result[$config->config_key] = $config->value;
        }
        
        return $result;
    }

    /**
     * Cập nhật config theo group
     */
    public static function updateGroup($group, $data)
    {
        foreach ($data as $key => $value) {
            static::where('config_key', $key)
                  ->where('config_group', $group)
                  ->update(['config_value' => $value]);
        }
    }
}
