<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'shipping_zone_id' => 'required|exists:shipping_zones,id',
            'total_amount' => 'required|numeric|min:0',
            'shipping_fee' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:pending,confirmed,processing,shipping,delivered,cancelled',
            'payment_status' => 'required|string|in:pending,paid,failed',
            'payment_method' => 'required|string|in:cash,bank_transfer,credit_card,cod',
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'shipping_address.address' => 'required|string',
            'shipping_address.province' => 'required|string',
            'shipping_address.district' => 'required|string',
            'billing_address' => 'nullable|array',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.warehouse_id' => 'required|exists:warehouses,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.product_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Vui lòng chọn khách hàng.',
            'user_id.exists' => 'Khách hàng không hợp lệ.',
            'warehouse_id.required' => 'Vui lòng chọn kho.',
            'warehouse_id.exists' => 'Kho không hợp lệ.',
            'shipping_zone_id.required' => 'Vui lòng chọn khu vực vận chuyển.',
            'shipping_zone_id.exists' => 'Khu vực vận chuyển không hợp lệ.',
            'total_amount.required' => 'Tổng tiền không được để trống.',
            'total_amount.numeric' => 'Tổng tiền phải là số.',
            'shipping_fee.required' => 'Phí vận chuyển không được để trống.',
            'shipping_fee.numeric' => 'Phí vận chuyển phải là số.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'payment_status.required' => 'Trạng thái thanh toán không được để trống.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',
            'payment_method.required' => 'Phương thức thanh toán không được để trống.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
            'shipping_address.required' => 'Địa chỉ giao hàng không được để trống.',
            'shipping_address.array' => 'Địa chỉ giao hàng phải là mảng.',
            'shipping_address.name.required' => 'Tên người nhận không được để trống.',
            'shipping_address.phone.required' => 'Số điện thoại không được để trống.',
            'shipping_address.address.required' => 'Địa chỉ không được để trống.',
            'shipping_address.province.required' => 'Tỉnh/Thành không được để trống.',
            'shipping_address.district.required' => 'Quận/Huyện không được để trống.',
            'items.required' => 'Đơn hàng phải có ít nhất 1 sản phẩm.',
            'items.array' => 'Danh sách sản phẩm phải là mảng.',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm.',
            'items.*.product_id.exists' => 'Sản phẩm không hợp lệ.',
            'items.*.warehouse_id.required' => 'Vui lòng chọn kho.',
            'items.*.warehouse_id.exists' => 'Kho không hợp lệ.',
            'items.*.quantity.required' => 'Số lượng không được để trống.',
            'items.*.quantity.integer' => 'Số lượng phải là số nguyên.',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
            'items.*.product_price.required' => 'Giá sản phẩm không được để trống.',
            'items.*.product_price.numeric' => 'Giá sản phẩm phải là số.',
        ];
    }
}
