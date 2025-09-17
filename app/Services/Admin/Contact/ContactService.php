<?php

namespace App\Services\Admin\Contact;

use App\Services\BaseService;
use App\Repositories\Contact\ContactRepository;
use App\Enums\ContactStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactResponseMail;
use Exception;

class ContactService extends BaseService
{
    public function __construct(ContactRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Update contact status
     */
    public function updateStatus($id, ContactStatus $status, $adminId = null, $adminNotes = null): ?array
    {
        try {
            $contact = $this->repo->find($id);
            if (!$contact) {
                throw new Exception('Contact not found');
            }
            $result = $this->repo->updateStatus($id, $status, $adminId, $adminNotes);
            // Send response email to customer if status is completed
            if ($status === ContactStatus::COMPLETED) {
                $this->sendResponseEmail($contact);
            }
            return $result;
        } catch (Exception $e) {
            Log::error('Error updating contact status: ' . $e->getMessage(), [
                'contact_id' => $id,
                'status' => $status->value,
                'admin_id' => $adminId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Mark contact as responded
     */
    public function markAsResponded($id, $adminId = null): ?array
    {
        try {
            return $this->repo->markAsResponded($id, $adminId);
        } catch (Exception $e) {
            Log::error('Error marking contact as responded: ' . $e->getMessage(), [
                'contact_id' => $id,
                'admin_id' => $adminId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Send response email to customer
     */
    protected function sendResponseEmail($contact): void
    {
        try {
            // Mail::to($contact->email)
            //     ->send(new ContactResponseMail($contact));
            echo "ok";
        } catch (Exception $e) {
            Log::error('Error sending response email: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update contact status
     */
    public function bulkUpdateStatus(array $contactIds, ContactStatus $status, $adminId = null, $adminNotes = null): array
    {
        $results = [];
        foreach ($contactIds as $contactId) {
            try {
                $result = $this->updateStatus($contactId, $status, $adminId, $adminNotes);
                $results[] = [
                    'id' => $contactId,
                    'success' => true,
                    'data' => $result
                ];
            } catch (Exception $e) {
                $results[] = [
                    'id' => $contactId,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        return $results;
    }
}
