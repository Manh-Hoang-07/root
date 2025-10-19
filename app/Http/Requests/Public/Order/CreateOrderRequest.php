<?php

namespace App\Http\Requests\Public\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'payment_method' => ['required', 'string', 'in:cod,bank_transfer,credit_card'],
            'shipping_method' => ['required', 'string', 'in:standard,express'],
        ];

        // For guest users, require items
        if (!$this->isAuthenticated()) {
            $rules['items'] = ['required', 'array', 'min:1'];
            $rules['items.*.product_id'] = ['required_without:items.*.product_variant_id', 'nullable', 'integer', 'exists:products,id'];
            $rules['items.*.product_variant_id'] = ['required_without:items.*.product_id', 'nullable', 'integer', 'exists:product_variants,id'];
            $rules['items.*.quantity'] = ['required', 'integer', 'min:1'];
            $rules['items.*.unit_price'] = ['required', 'numeric', 'min:0'];
        }
        // For authenticated users, cart_id is optional (will be auto-generated)
        else {
            $rules['cart_id'] = ['nullable', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'payment_method.required' => 'Phương thức thanh toán là bắt buộc',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'shipping_method.required' => 'Phương thức vận chuyển là bắt buộc',
            'shipping_method.in' => 'Phương thức vận chuyển không hợp lệ',
        ];

        if (!$this->isAuthenticated()) {
            $messages['items.required'] = 'Đơn hàng phải có ít nhất một sản phẩm';
            $messages['items.min'] = 'Đơn hàng phải có ít nhất một sản phẩm';
            $messages['items.*.product_id.required_without'] = 'Vui lòng chọn sản phẩm hoặc biến thể sản phẩm';
            $messages['items.*.product_id.exists'] = 'Sản phẩm không tồn tại';
            $messages['items.*.product_variant_id.required_without'] = 'Vui lòng chọn sản phẩm hoặc biến thể sản phẩm';
            $messages['items.*.product_variant_id.exists'] = 'Biến thể sản phẩm không tồn tại';
            $messages['items.*.quantity.required'] = 'Số lượng là bắt buộc';
            $messages['items.*.quantity.min'] = 'Số lượng phải lớn hơn 0';
            $messages['items.*.unit_price.required'] = 'Đơn giá là bắt buộc';
            $messages['items.*.unit_price.min'] = 'Đơn giá phải lớn hơn hoặc bằng 0';
        }

        return $messages;
    }

    public function attributes(): array
    {
        $attributes = [
            'payment_method' => 'phương thức thanh toán',
            'shipping_method' => 'phương thức vận chuyển',
        ];

        if (!$this->isAuthenticated()) {
            $attributes['items'] = 'sản phẩm';
            $attributes['items.*.product_id'] = 'sản phẩm';
            $attributes['items.*.product_variant_id'] = 'biến thể sản phẩm';
            $attributes['items.*.quantity'] = 'số lượng';
            $attributes['items.*.unit_price'] = 'đơn giá';
        } else {
            $attributes['cart_id'] = 'giỏ hàng';
        }

        return $attributes;
    }

    /**
     * Check if the user is authenticated
     */
    private function isAuthenticated(): bool
    {
        return \Illuminate\Support\Facades\Auth::check();
    }
}
