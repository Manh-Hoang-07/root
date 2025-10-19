<?php

namespace App\Http\Requests\Public\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CartUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Số lượng là bắt buộc',
            'quantity.min' => 'Số lượng phải lớn hơn 0',
        ];
    }

    public function attributes(): array
    {
        return [
            'quantity' => 'số lượng',
        ];
    }
}