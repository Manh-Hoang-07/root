<?php

namespace App\Repositories\Contact;

use App\Models\Contact;
use App\Repositories\BaseRepository;
use App\Enums\ContactStatus;

class ContactRepository extends BaseRepository
{
    public function model(): string
    {
        return Contact::class;
    }

    /**
     * Get searchable fields for contact
     */
    protected function getSearchableFields(): array
    {
        return ['name', 'email', 'phone', 'subject', 'content'];
    }

    /**
     * Apply relationship search for contacts
     */
    protected function applyRelationshipSearch(\Illuminate\Database\Eloquent\Builder $query, string $searchValue): void
    {
        $query->orWhereHas('admin', function ($q) use ($searchValue) {
            $q->where('name', 'like', "%$searchValue%")
              ->orWhere('email', 'like', "%$searchValue%");
        });
    }

    /**
     * Update contact status
     */
    public function updateStatus(int $id, ContactStatus $status, $adminId = null, $adminNotes = null): ?array
    {
        $data = ['status' => $status];
        if ($adminId) {
            $data['admin_id'] = $adminId;
        }
        if ($adminNotes) {
            $data['admin_notes'] = $adminNotes;
        }
        // Mark as responded if status is completed
        if ($status === ContactStatus::COMPLETED) {
            $data['responded_at'] = now();
        }
        return $this->update($id, $data);
    }

    /**
     * Mark contact as responded
     */
    public function markAsResponded(int $id, $adminId = null): ?array
    {
        $data = ['responded_at' => now()];
        if ($adminId) {
            $data['admin_id'] = $adminId;
        }
        return $this->update($id, $data);
    }

}
