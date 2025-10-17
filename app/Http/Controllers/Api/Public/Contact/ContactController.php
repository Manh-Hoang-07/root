<?php

namespace App\Http\Controllers\Api\Public\Contact;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Public\Contact\ContactRequest;
use App\Services\Public\Contact\ContactService;

class ContactController extends CrudController
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
