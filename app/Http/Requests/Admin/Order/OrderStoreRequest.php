<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'shipping_address' => ['required', 'array'],
            'billing_address' => ['nullable', 'array'],
            'currency' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:2000'],

            // monetary fields are derived but allow initial values
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],

            // items
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required_without:items.*.product_variant_id', 'nullable', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['required_without:items.*.product_id', 'nullable', 'integer', 'exists:product_variants,id'],
            'items.*.product_name' => ['nullable', 'string', 'max:255'],
            'items.*.product_sku' => ['nullable', 'string', 'max:100'],
            'items.*.variant_name' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.product_attributes' => ['nullable', 'array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'items' => 'sản phẩm',
            'items.*.quantity' => 'số lượng',
            'items.*.unit_price' => 'đơn giá',
        ];
    }
}


