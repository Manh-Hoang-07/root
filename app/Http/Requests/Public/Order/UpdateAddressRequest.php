<?php

namespace App\Http\Requests\Public\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.address' => ['required', 'string', 'max:500'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'max:100'],
            'billing_address' => ['nullable', 'array'],
            'billing_address.address' => ['required_with:billing_address', 'string', 'max:500'],
            'billing_address.city' => ['required_with:billing_address', 'string', 'max:100'],
            'billing_address.postal_code' => ['nullable', 'string', 'max:20'],
            'billing_address.country' => ['required_with:billing_address', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Họ tên là bắt buộc',
            'customer_email.required' => 'Email là bắt buộc',
            'customer_email.email' => 'Email không hợp lệ',
            'customer_phone.required' => 'Số điện thoại là bắt buộc',
            'shipping_address.required' => 'Địa chỉ giao hàng là bắt buộc',
            'shipping_address.address.required' => 'Địa chỉ giao hàng là bắt buộc',
            'shipping_address.city.required' => 'Thành phố là bắt buộc',
            'shipping_address.country.required' => 'Quốc gia là bắt buộc',
            'billing_address.address.required_with' => 'Địa chỉ thanh toán là bắt buộc khi có thông tin thanh toán',
            'billing_address.city.required_with' => 'Thành phố thanh toán là bắt buộc khi có thông tin thanh toán',
            'billing_address.country.required_with' => 'Quốc gia thanh toán là bắt buộc khi có thông tin thanh toán',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name' => 'họ tên',
            'customer_email' => 'email',
            'customer_phone' => 'số điện thoại',
            'shipping_address' => 'địa chỉ giao hàng',
            'billing_address' => 'địa chỉ thanh toán',
            'notes' => 'ghi chú',
        ];
    }
}
