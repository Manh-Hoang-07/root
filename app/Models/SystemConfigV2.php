<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SystemConfigV2 extends Model
{
    protected $table = 'system_config_v2';
    
    protected $fillable = [
        'key',
        'value',
        'group',
        'is_public',
        'description',
        'type'
    ];
    
    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($this->type === 'json') {
                    return json_decode($value, true);
                }
                if ($this->type === 'boolean') {
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                if ($this->type === 'number') {
                    return is_numeric($value) ? (float)$value : $value;
                }
                return $value;
            },
            set: function ($value) {
                if ($this->type === 'json') {
                    return json_encode($value);
                }
                if ($this->type === 'boolean') {
                    return $value ? '1' : '0';
                }
                return (string)$value;
            }
        );
    }
    
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
    
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }
    
    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }
}
