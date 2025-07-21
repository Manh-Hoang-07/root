<?php

namespace App\Http\Requests\Admin\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class ShippingZoneRequest extends FormRequest
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
            'provinces' => 'required|array',
            'provinces.*' => 'string',
            'districts' => 'nullable|array',
            'districts.*' => 'string',
            'base_fee' => 'nullable|numeric|min:0',
            'weight_fee' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:0',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên zone không được để trống.',
            'name.max' => 'Tên zone không được vượt quá :max ký tự.',
            'provinces.required' => 'Vui lòng chọn tỉnh/thành cho zone.',
            'provinces.array' => 'Danh sách tỉnh/thành phải là mảng.',
            'provinces.*.string' => 'Mỗi tỉnh/thành phải là chuỗi.',
            'districts.array' => 'Danh sách quận/huyện phải là mảng.',
            'districts.*.string' => 'Mỗi quận/huyện phải là chuỗi.',
            'base_fee.numeric' => 'Phí cơ bản phải là số.',
            'base_fee.min' => 'Phí cơ bản không được nhỏ hơn 0.',
            'weight_fee.numeric' => 'Phí theo cân nặng phải là số.',
            'weight_fee.min' => 'Phí theo cân nặng không được nhỏ hơn 0.',
            'estimated_days.integer' => 'Số ngày dự kiến phải là số nguyên.',
            'estimated_days.min' => 'Số ngày dự kiến không được nhỏ hơn 0.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
