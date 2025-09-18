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
     * Override BaseService create to add default status and notifications
     */
    public function create($data): array
    {
        try {
            $data['status'] = ContactStatus::PENDING;
            $contact = parent::create($data);
            $this->sendNotificationEmail($contact);
            return $contact;
        } catch (Exception $e) {
            Log::error('Error creating contact: ' . $e->getMessage(), [
                'data' => $data,
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
                'contact_id' => is_array($contact) ? ($contact['id'] ?? null) : ($contact->id ?? null),
                'email' => is_array($contact) ? ($contact['email'] ?? null) : ($contact->email ?? null),
                'subject' => is_array($contact) ? ($contact['subject'] ?? null) : ($contact->subject ?? null)
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
