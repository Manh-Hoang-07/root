<?php

namespace App\Repositories\Public\Contact;

use App\Models\Contact;
use App\Repositories\BaseRepository;
use App\Enums\ContactStatus;

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
}
