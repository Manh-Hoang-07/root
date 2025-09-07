<?php

namespace App\Repositories\Admin\Contact;

use App\Models\Contact;
use App\Repositories\BaseRepository;
use App\Enums\ContactStatus;
use Illuminate\Database\Eloquent\Builder;

class ContactRepository extends BaseRepository
{
    public function model()
    {
        return Contact::class;
    }

    /**
     * Get searchable fields for contact
     */
    protected function getSearchableFields()
    {
        return ['name', 'email', 'phone', 'subject', 'content'];
    }

    /**
     * Apply relationship search for contacts
     */
    protected function applyRelationshipSearch($query, $searchValue)
    {
        $query->orWhereHas('admin', function ($q) use ($searchValue) {
            $q->where('name', 'like', "%$searchValue%")
              ->orWhere('email', 'like', "%$searchValue%");
        });
    }

    /**
     * Update contact status
     */
    public function updateStatus($id, ContactStatus $status, $adminId = null, $adminNotes = null)
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
    public function markAsResponded($id, $adminId = null)
    {
        $data = ['responded_at' => now()];
        
        if ($adminId) {
            $data['admin_id'] = $adminId;
        }

        return $this->update($id, $data);
    }

}
