<?php

namespace App\Http\Controllers\Api\Public\Contact;

use App\Http\Controllers\Api\BaseController;
use App\Services\Public\Contact\ContactService;
use App\Http\Requests\Public\Contact\ContactRequest;

class ContactController extends BaseController
{
    /**
     * @var ContactService
     */
    protected $service;
    protected $storeRequestClass = ContactRequest::class;

    public function __construct(ContactService $service)
    {
        parent::__construct($service);
    }
}
