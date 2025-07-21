<?php

namespace App\Http\Requests\Admin\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class ShippingPromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'discount' => 'required|numeric|min:0',
            'type' => 'required|string|max:50',
            'service_id' => 'nullable|exists:shipping_services,id',
            'zone_id' => 'nullable|exists:shipping_zones,id',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên khuyến mãi không được để trống.',
            'discount.required' => 'Giá trị giảm giá không được để trống.',
            'discount.numeric' => 'Giá trị giảm giá phải là số.',
            'discount.min' => 'Giá trị giảm giá không được nhỏ hơn 0.',
            'type.required' => 'Loại khuyến mãi không được để trống.',
            'service_id.exists' => 'Dịch vụ không hợp lệ.',
            'zone_id.exists' => 'Zone không hợp lệ.',
            'valid_from.date' => 'Ngày bắt đầu không hợp lệ.',
            'valid_until.date' => 'Ngày kết thúc không hợp lệ.',
            'valid_until.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
