<?php

namespace App\Http\Controllers\Api\Public\Contact;

use App\Http\Controllers\Api\BaseController;
use App\Services\Public\Contact\ContactService;
use App\Http\Resources\Public\Contact\ContactResource;
use App\Http\Requests\Public\Contact\ContactRequest;
use Illuminate\Http\JsonResponse;
use Exception;

class ContactController extends BaseController
{
    protected $service;
        protected $storeRequestClass = ContactRequest::class;

    public function __construct(ContactService $service)
    {
        parent::__construct($service);
        $this->service = $service;
    }

    /**
     * Override store method to use createContact service method
     */
    public function store()
    {
        try {
            $request = app($this->getStoreRequestClass());
            $data = $this->service->createContact($request->all());
            
            return $this->successResponseWithFormat($data, 'single');
        } catch (Exception $e) {
            $this->logError('Store', $e);
            return $this->apiResponse(false, null, 'Không thể gửi liên hệ. Vui lòng thử lại sau.', 500);
        }
    }
}
