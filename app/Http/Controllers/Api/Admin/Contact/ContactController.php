<?php

namespace App\Http\Controllers\Api\Admin\Contact;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Contact\ContactService;
use App\Http\Resources\Admin\Contact\ContactResource;
use App\Http\Resources\Admin\Contact\ContactListResource;
use App\Http\Requests\Admin\Contact\ContactRequest;
use App\Http\Requests\Admin\Contact\ContactStatusUpdateRequest;
use App\Enums\ContactStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class ContactController extends BaseController
{
    protected $service;
    protected $resource = ContactResource::class;
    protected $listResource = ContactListResource::class;
    protected $storeRequestClass = ContactRequest::class;
    protected $updateRequestClass = ContactRequest::class;
    
    protected $indexRelations = ['admin'];
    protected $showRelations = ['admin'];
    
    protected $defaultPerPage = 20;
    protected $maxPerPage = 100;

    public function __construct(ContactService $service)
    {
        parent::__construct($service, ContactResource::class);
        $this->service = $service;
    }

    /**
     * Display a listing of contacts
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->all();
            $perPage = min($request->get('per_page', $this->defaultPerPage), $this->maxPerPage);
            
            // Parse relations from request
            $relations = $this->parseRelations($filters['relations'] ?? null);
            $relations = !empty($relations) ? $relations : $this->indexRelations;
            
            // Parse fields from request
            $fields = $this->parseFields($filters['fields'] ?? null);
            if (empty($fields) || $fields === ['*']) {
                $fields = $this->getDefaultListFields();
            }
            
            // Remove non-filter parameters
            unset($filters['relations'], $filters['fields'], $filters['per_page']);
            
            $data = $this->service->list($filters, $perPage, $relations, $fields);
            
            return $this->formatResponse($data);
        } catch (Exception $e) {
            $this->logError('Index', $e);
            return $this->errorResponse('Không thể tải danh sách liên hệ');
        }
    }

    /**
     * Display the specified contact
     */
    public function show($id, Request $request = null)
    {
        try {
            $relations = $this->parseRelations($request?->get('relations') ?? null);
            $relations = !empty($relations) ? $relations : $this->showRelations;
            
            $fields = $this->parseFields($request?->get('fields') ?? null);
            if (empty($fields) || $fields === ['*']) {
                $fields = $this->getDefaultShowFields();
            }
            
            $contact = $this->service->find($id, $relations, $fields);
            
            if (!$contact) {
                return $this->errorResponse('Không tìm thấy liên hệ', 404);
            }
            
            return $this->formatResponse($contact, 'single');
        } catch (Exception $e) {
            $this->logError('Show', $e, ['id' => $id]);
            return $this->errorResponse('Không thể tải thông tin liên hệ');
        }
    }

    /**
     * Update the specified contact
     */
    public function update($id)
    {
        try {
            $request = app($this->getUpdateRequestClass());
            $data = $this->service->update($id, $request->validated());
            
            if (!$data) {
                return $this->errorResponse('Không tìm thấy liên hệ để cập nhật', 404);
            }
            
            return $this->formatResponse($data, 'single');
        } catch (Exception $e) {
            $this->logError('Update', $e, ['id' => $id]);
            return $this->errorResponse('Không thể cập nhật liên hệ');
        }
    }

    /**
     * Remove the specified contact
     */
    public function destroy($id)
    {
        try {
            $result = $this->service->delete($id);
            
            if ($result) {
                return $this->deletedResponse();
            }
            
            return $this->errorResponse('Không thể xóa liên hệ');
        } catch (Exception $e) {
            $this->logError('Destroy', $e, ['id' => $id]);
            return $this->errorResponse('Không thể xóa liên hệ');
        }
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
                return $this->errorResponse('Không tìm thấy liên hệ để cập nhật', 404);
            }
            
            return $this->formatResponse($data, 'single');
        } catch (Exception $e) {
            $this->logError('UpdateStatus', $e, ['id' => $id]);
            return $this->errorResponse('Không thể cập nhật trạng thái liên hệ');
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
                return $this->errorResponse('Không tìm thấy liên hệ để cập nhật', 404);
            }
            
            return $this->formatResponse($data, 'single');
        } catch (Exception $e) {
            $this->logError('MarkAsResponded', $e, ['id' => $id]);
            return $this->errorResponse('Không thể đánh dấu liên hệ đã phản hồi');
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
            
            return $this->successResponse($results, 'Cập nhật trạng thái hàng loạt');
        } catch (Exception $e) {
            $this->logError('BulkUpdateStatus', $e);
            return $this->errorResponse('Không thể cập nhật trạng thái hàng loạt');
        }
    }

    /**
     * Get default fields for list view
     */
    protected function getDefaultListFields(): array
    {
        return ['id', 'name', 'email', 'phone', 'subject', 'content', 'status', 'admin_notes', 'created_at', 'updated_at'];
    }
}
