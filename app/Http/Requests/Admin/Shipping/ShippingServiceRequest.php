<?php

namespace App\Http\Requests\Admin\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class ShippingServiceRequest extends FormRequest
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
        $serviceId = $this->route('service') ? $this->route('service')->id : null;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:shipping_services,code,' . $serviceId,
            'provider_id' => 'required|exists:shipping_api_configs,id',
            'base_price' => 'nullable|numeric|min:0',
            'weight_fee' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:0',
            'status' => 'required|string|in:active,inactive',
            'image' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên dịch vụ không được để trống.',
            'name.max' => 'Tên dịch vụ không được vượt quá :max ký tự.',
            'code.required' => 'Mã dịch vụ không được để trống.',
            'code.max' => 'Mã dịch vụ không được vượt quá :max ký tự.',
            'code.unique' => 'Mã dịch vụ đã tồn tại.',
            'provider_id.required' => 'Vui lòng chọn provider.',
            'provider_id.exists' => 'Provider không hợp lệ.',
            'base_price.numeric' => 'Giá cơ bản phải là số.',
            'base_price.min' => 'Giá cơ bản không được nhỏ hơn 0.',
            'weight_fee.numeric' => 'Phí theo cân nặng phải là số.',
            'weight_fee.min' => 'Phí theo cân nặng không được nhỏ hơn 0.',
            'estimated_days.integer' => 'Số ngày dự kiến phải là số nguyên.',
            'estimated_days.min' => 'Số ngày dự kiến không được nhỏ hơn 0.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
