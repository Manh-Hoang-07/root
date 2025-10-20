<?php

namespace App\Http\Controllers\Api\Admin\Contact;

use App\Enums\ContactStatus;
use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Contact\ContactRequest;
use App\Http\Requests\Admin\Contact\ContactStatusUpdateRequest;
use App\Services\Admin\Contact\ContactService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends CrudController
{
    protected $storeRequestClass = ContactRequest::class;
    protected $updateRequestClass = ContactRequest::class;
    protected $statusUpdateRequestClass = ContactStatusUpdateRequest::class;
    protected $indexRelations = ['admin'];
    protected $showRelations = ['admin'];

    /**
     * @var ContactService
     */
    protected $service;

    public function __construct(ContactService $service)
    {
        parent::__construct($service);
    }

    /**
     * Mark contact as responded
     */
    public function markAsResponded($id)
    {
        try {
            $result = $this->service->markAsResponded($id);
            if (!$result['success']) {
                return $this->apiResponse(false, null, $result['message'], 404);
            }
            return $this->successResponseWithFormat($result['data'], $result['message']);
        } catch (Exception $e) {
            $this->logError('MarkAsResponded', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể đánh dấu liên hệ đã phản hồi', 500);
        }
    }

    /**
     * Bulk update contact status
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'contact_ids' => 'required|array|min:1',
                'contact_ids.*' => 'integer|exists:contacts,id',
                'status' => 'required|string|in:pending,in_progress,completed,cancelled',
                'admin_notes' => 'nullable|string|max:2000',
            ]);
            $contactIds = $request->contact_ids;
            $status = $request->status; // Use string directly instead of enum
            $adminNotes = $request->admin_notes;
            $result = $this->service->bulkUpdateStatus($contactIds, $status, null, $adminNotes);
            return $this->apiResponse(true, $result['data'], $result['message']);
        } catch (Exception $e) {
            $this->logError('BulkUpdateStatus', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật trạng thái hàng loạt', 500);
        }
    }
}
