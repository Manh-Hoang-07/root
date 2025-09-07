<?php

namespace App\Services\Public\Contact;

use App\Services\BaseService;
use App\Repositories\Contact\ContactRepository;
use App\Enums\ContactStatus;
use Illuminate\Support\Facades\Log;
use Exception;

class ContactService extends BaseService
{
    public function __construct(ContactRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Create a new contact (Public API)
     */
    public function createContact(array $data)
    {
        try {
            // Set default status
            $data['status'] = ContactStatus::PENDING;
            
            // Create contact using BaseService method
            $contact = $this->create($data);
            
            // Send notification email to admin (optional)
            $this->sendNotificationEmail($contact);
            
            return $contact;
        } catch (Exception $e) {
            Log::error('Error creating contact: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Send notification email to admin
     */
    protected function sendNotificationEmail($contact)
    {
        try {
            // This would be implemented based on your email configuration
            // For now, just log the notification
            Log::info('Contact notification email should be sent', [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'subject' => $contact->subject
            ]);
            
            // Uncomment and implement when email is configured
            /*
            Mail::to(config('mail.admin_email', 'admin@example.com'))
                ->send(new ContactNotificationMail($contact));
            */
        } catch (Exception $e) {
            Log::error('Error sending notification email: ' . $e->getMessage());
        }
    }
}
