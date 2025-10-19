<?php

namespace App\Http\Requests\Public\Order;

use Illuminate\Foundation\Http\FormRequest;

class GuestOrderRequest extends FormRequest
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
            'payment_method' => ['required', 'string', 'in:cod,bank_transfer,credit_card'],
            'shipping_method' => ['required', 'string', 'in:standard,express'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required_without:items.*.product_variant_id', 'nullable', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['required_without:items.*.product_id', 'nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
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
            'payment_method.required' => 'Phương thức thanh toán là bắt buộc',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'shipping_method.required' => 'Phương thức vận chuyển là bắt buộc',
            'shipping_method.in' => 'Phương thức vận chuyển không hợp lệ',
            'items.required' => 'Đơn hàng phải có ít nhất một sản phẩm',
            'items.min' => 'Đơn hàng phải có ít nhất một sản phẩm',
            'items.*.product_id.required_without' => 'Vui lòng chọn sản phẩm hoặc biến thể sản phẩm',
            'items.*.product_id.exists' => 'Sản phẩm không tồn tại',
            'items.*.product_variant_id.required_without' => 'Vui lòng chọn sản phẩm hoặc biến thể sản phẩm',
            'items.*.product_variant_id.exists' => 'Biến thể sản phẩm không tồn tại',
            'items.*.quantity.required' => 'Số lượng là bắt buộc',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0',
            'items.*.unit_price.required' => 'Đơn giá là bắt buộc',
            'items.*.unit_price.min' => 'Đơn giá phải lớn hơn hoặc bằng 0',
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
            'payment_method' => 'phương thức thanh toán',
            'shipping_method' => 'phương thức vận chuyển',
            'items' => 'sản phẩm',
            'items.*.product_id' => 'sản phẩm',
            'items.*.product_variant_id' => 'biến thể sản phẩm',
            'items.*.quantity' => 'số lượng',
            'items.*.unit_price' => 'đơn giá',
        ];
    }
}