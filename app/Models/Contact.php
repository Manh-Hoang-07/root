<?php

namespace App\Models;

use App\Enums\ContactStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'content',
        'status',
        'admin_notes',
        'admin_id',
        'responded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ContactStatus::class,
        'responded_at' => 'datetime',
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
     * Get the admin user who handled this contact
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope a query to only include contacts with specific status
     */
    public function scopeStatus($query, ContactStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending contacts
     */
    public function scopePending($query)
    {
        return $query->where('status', ContactStatus::PENDING);
    }

    /**
     * Scope a query to only include active contacts
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [ContactStatus::PENDING, ContactStatus::IN_PROGRESS]);
    }

    /**
     * Scope a query to only include completed contacts
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', ContactStatus::COMPLETED);
    }

    /**
     * Check if contact is pending
     */
    public function isPending(): bool
    {
        return $this->status === ContactStatus::PENDING;
    }

    /**
     * Check if contact is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === ContactStatus::IN_PROGRESS;
    }

    /**
     * Check if contact is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === ContactStatus::COMPLETED;
    }

    /**
     * Check if contact is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === ContactStatus::CANCELLED;
    }

    /**
     * Check if contact is active (pending or in progress)
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * Mark contact as responded
     */
    public function markAsResponded(): void
    {
        $this->update([
            'responded_at' => now(),
        ]);
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format Vietnamese phone number
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 3);
        }
        
        if (strlen($phone) === 11 && substr($phone, 0, 2) === '84') {
            return '+84 ' . substr($phone, 2, 4) . ' ' . substr($phone, 6, 3) . ' ' . substr($phone, 9, 3);
        }
        
        return $this->phone;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabel();
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status->getColor();
    }
}
