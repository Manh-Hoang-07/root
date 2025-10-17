<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required_without:product_variant_id', 'nullable', 'integer', 'exists:products,id'],
            'product_variant_id' => ['required_without:product_id', 'nullable', 'integer', 'exists:product_variants,id'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'product_sku' => ['nullable', 'string', 'max:100'],
            'variant_name' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'product_attributes' => ['nullable', 'array'],
        ];
    }
}


