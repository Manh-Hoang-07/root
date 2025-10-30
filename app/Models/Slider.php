<?php

namespace App\Models;

use App\Enums\BasicStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'image_path',
        'link',
        'status',
        'start_time',
        'end_time',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => BasicStatus::class,
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Scope a query to only include active sliders that are currently displayable
     * Based on status, start_time, and end_time
     */
    public function scopeActive($query)
    {
        $now = now();

        return $query->where('status', BasicStatus::Active)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_time')
                    ->orWhere('start_time', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_time')
                    ->orWhere('end_time', '>=', $now);
            });
    }

    /**
     * Scope a query to only include sliders with specific status
     */
    public function scopeStatus($query, BasicStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if slider is active
     */
    public function isActive(): bool
    {
        return $this->status === BasicStatus::Active;
    }

    /**
     * Check if slider is currently displayable based on time ranges
     */
    public function isCurrentlyDisplayable(): bool
    {
        // If inactive, not displayable
        if (!$this->isActive()) {
            return false;
        }

        $now = now();

        // Check start time
        if ($this->start_time && $this->start_time->gt($now)) {
            return false;
        }

        // Check end time
        if ($this->end_time && $this->end_time->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }
}
