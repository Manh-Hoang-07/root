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
     * Get contacts by admin
     */
    public function getByAdmin($adminId, $perPage = 20, $relations = [], $fields = ['*'])
    {
        $filters = ['admin_id' => $adminId];
        return $this->all($filters, $perPage, $relations, $fields);
    }

    /**
     * Get contacts by date range
     */
    public function getByDateRange($startDate, $endDate, $perPage = 20, $relations = [], $fields = ['*'])
    {
        $filters = [
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ];
        return $this->all($filters, $perPage, $relations, $fields);
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

    /**
     * Get contacts with admin notes
     */
    public function getWithAdminNotes($perPage = 20, $relations = [], $fields = ['*'])
    {
        $query = $this->model->query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        
        $query->whereNotNull('admin_notes');
        
        return $query->paginate($perPage);
    }

    /**
     * Get contacts without admin notes
     */
    public function getWithoutAdminNotes($perPage = 20, $relations = [], $fields = ['*'])
    {
        $query = $this->model->query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        
        $query->whereNull('admin_notes');
        
        return $query->paginate($perPage);
    }


    /**
     * Get contacts by email
     */
    public function getByEmail($email, $perPage = 20, $relations = [], $fields = ['*'])
    {
        $filters = ['email' => $email];
        return $this->all($filters, $perPage, $relations, $fields);
    }

    /**
     * Get contacts by phone
     */
    public function getByPhone($phone, $perPage = 20, $relations = [], $fields = ['*'])
    {
        $filters = ['phone' => $phone];
        return $this->all($filters, $perPage, $relations, $fields);
    }
}
