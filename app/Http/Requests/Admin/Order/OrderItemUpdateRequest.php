<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'unit_price' => ['sometimes', 'numeric', 'min:0'],
            'product_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'product_sku' => ['sometimes', 'nullable', 'string', 'max:100'],
            'variant_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'product_attributes' => ['sometimes', 'nullable', 'array'],
        ];
    }
}


