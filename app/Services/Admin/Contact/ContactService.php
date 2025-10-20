<?php

namespace App\Services\Admin\Contact;

use App\Services\BaseService;
use App\Repositories\Contact\ContactRepository;
use App\Enums\ContactStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\ContactResponseMail;
use Exception;

class ContactService extends BaseService
{
    /**
     * @var ContactRepository
     */
    protected $repo;
    public function __construct(ContactRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Update contact status with admin tracking
     */
    public function updateContactStatus($id, string $status, $adminId = null, $adminNotes = null): array
    {
        // If adminId is not provided, get it from Auth
        if ($adminId === null) {
            $adminId = Auth::id();
        }
        
        try {
            $contact = $this->repo->find($id);
            if (!$contact) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy liên hệ',
                    'data' => null
                ];
            }
            $result = $this->repo->updateStatus($id, $status, $adminId, $adminNotes);
            // Send response email to customer if status is completed
            if ($status === 'completed') {
                $this->sendResponseEmail($contact);
            }
            
            return [
                'success' => true,
                'message' => 'Cập nhật trạng thái liên hệ thành công',
                'data' => $result
            ];
        } catch (Exception $e) {
            Log::error('Error updating contact status: ' . $e->getMessage(), [
                'contact_id' => $id,
                'status' => $status,
                'admin_id' => $adminId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái liên hệ',
                'data' => null
            ];
        }
    }

    /**
     * Mark contact as responded
     */
    public function markAsResponded($id, $adminId = null): array
    {
        // If adminId is not provided, get it from Auth
        if ($adminId === null) {
            $adminId = Auth::id();
        }
        
        try {
            $result = $this->repo->markAsResponded($id, $adminId);
            
            return [
                'success' => true,
                'message' => 'Đánh dấu liên hệ đã phản hồi thành công',
                'data' => $result
            ];
        } catch (Exception $e) {
            Log::error('Error marking contact as responded: ' . $e->getMessage(), [
                'contact_id' => $id,
                'admin_id' => $adminId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Không thể đánh dấu liên hệ đã phản hồi',
                'data' => null
            ];
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
    public function bulkUpdateStatus(array $contactIds, string $status, $adminId = null, $adminNotes = null): array
    {
        // If adminId is not provided, get it from Auth
        if ($adminId === null) {
            $adminId = Auth::id();
        }
        
        $results = [];
        foreach ($contactIds as $contactId) {
            try {
                $result = $this->updateContactStatus($contactId, $status, $adminId, $adminNotes);
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
        
        return [
            'success' => true,
            'message' => 'Cập nhật trạng thái hàng loạt thành công',
            'data' => $results
        ];
    }
}
