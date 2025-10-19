<?php

namespace App\Http\Requests\Public\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CartCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Mã giảm giá là bắt buộc',
            'code.max' => 'Mã giảm giá không được vượt quá 50 ký tự',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'mã giảm giá',
        ];
    }
}