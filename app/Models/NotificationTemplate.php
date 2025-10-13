<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'type',
        'code',
        'name',
        'subject',
        'content',
        'variables',
        'locale',
        'status',
        'created_user_id',
        'updated_user_id',
    ];

    protected $casts = [
        'variables' => 'array',
        'status' => 'boolean',
    ];

    /**
     * Get the user who created this template
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * Get the user who last updated this template
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific locale
     */
    public function scopeOfLocale($query, string $locale = 'vi')
    {
        return $query->where('locale', $locale);
    }

    /**
     * Get template by code
     */
    public static function getByCode(string $code, string $locale = 'vi'): ?self
    {
        return static::active()
            ->where('code', $code)
            ->ofLocale($locale)
            ->first();
    }

    /**
     * Replace placeholders in content
     */
    public function replacePlaceholders(array $data = []): array
    {
        $subject = $this->subject;
        $content = $this->content;

        // Merge with default placeholders
        $defaults = [
            '{{app_name}}' => \App\Libraries\Public\SystemConfig::getName(),
            '{{current_date}}' => now()->format('d/m/Y'),
            '{{current_time}}' => now()->format('H:i:s'),
            '{{current_datetime}}' => now()->format('d/m/Y H:i:s'),
        ];

        $allData = array_merge($defaults, $data);

        // Replace placeholders
        foreach ($allData as $key => $value) {
            $placeholder = "{{$key}}";
            $subject = str_replace($placeholder, $value, $subject);
            $content = str_replace($placeholder, $value, $content);
        }

        return [
            'subject' => $subject,
            'content' => $content,
        ];
    }
}