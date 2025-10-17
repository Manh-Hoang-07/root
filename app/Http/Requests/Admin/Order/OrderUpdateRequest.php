<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'customer_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'customer_phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'shipping_address' => ['sometimes', 'array'],
            'billing_address' => ['sometimes', 'nullable', 'array'],
            'currency' => ['sometimes', 'string', 'max:10'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'shipping_amount' => ['sometimes', 'numeric', 'min:0'],
            'tax_amount' => ['sometimes', 'numeric', 'min:0'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}


