<?php

namespace App\Http\Requests\Public\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CartStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Sản phẩm là bắt buộc',
            'product_id.exists' => 'Sản phẩm không tồn tại',
            'product_variant_id.exists' => 'Biến thể sản phẩm không tồn tại',
            'quantity.required' => 'Số lượng là bắt buộc',
            'quantity.min' => 'Số lượng phải lớn hơn 0',
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'sản phẩm',
            'product_variant_id' => 'biến thể sản phẩm',
            'quantity' => 'số lượng',
        ];
    }
}
