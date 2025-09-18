<?php

namespace App\Http\Controllers\Api\Admin\Contact;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Contact\ContactService;
use App\Http\Requests\Admin\Contact\ContactRequest;
use App\Http\Requests\Admin\Contact\ContactStatusUpdateRequest;
use App\Enums\ContactStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ContactController extends BaseController
{
    protected $storeRequestClass = ContactRequest::class;
    protected $updateRequestClass = ContactRequest::class;
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
     * Update contact status
     */
    public function updateStatus($id, ContactStatusUpdateRequest $request)
    {
        try {
            $validated = $request->validated();
            $status = ContactStatus::from($validated['status']);
            $adminId = Auth::id();
            $adminNotes = $validated['admin_notes'] ?? null;
            $data = $this->service->updateStatus($id, $status, $adminId, $adminNotes);
            if (!$data) {
                return $this->apiResponse(false, null, 'Không tìm thấy liên hệ để cập nhật', 404);
            }
            return $this->successResponseWithFormat($data, 'single');
        } catch (Exception $e) {
            $this->logError('UpdateStatus', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể cập nhật trạng thái liên hệ', 500);
        }
    }

    /**
     * Mark contact as responded
     */
    public function markAsResponded($id)
    {
        try {
            $adminId = Auth::id();
            $data = $this->service->markAsResponded($id, $adminId);
            if (!$data) {
                return $this->apiResponse(false, null, 'Không tìm thấy liên hệ để cập nhật', 404);
            }
            return $this->successResponseWithFormat($data, 'single');
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
            $status = ContactStatus::from($request->status);
            $adminId = Auth::id();
            $adminNotes = $request->admin_notes;
            $results = $this->service->bulkUpdateStatus($contactIds, $status, $adminId, $adminNotes);
            return $this->apiResponse(true, $results, 'Cập nhật trạng thái hàng loạt');
        } catch (Exception $e) {
            $this->logError('BulkUpdateStatus', $e);
            return $this->apiResponse(false, null, 'Không thể cập nhật trạng thái hàng loạt', 500);
        }
    }

}
