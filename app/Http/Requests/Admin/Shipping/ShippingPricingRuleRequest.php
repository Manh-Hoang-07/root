<?php

namespace App\Http\Requests\Admin\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class ShippingPricingRuleRequest extends FormRequest
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
            'service_id' => 'required|exists:shipping_services,id',
            'zone_id' => 'required|exists:shipping_zones,id',
            'min_weight' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0|gte:min_weight',
            'markup_percent' => 'nullable|numeric|min:0',
            'markup_fixed' => 'nullable|numeric|min:0',
            'min_fee' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Vui lòng chọn dịch vụ.',
            'service_id.exists' => 'Dịch vụ không hợp lệ.',
            'zone_id.required' => 'Vui lòng chọn zone.',
            'zone_id.exists' => 'Zone không hợp lệ.',
            'min_weight.numeric' => 'Khối lượng tối thiểu phải là số.',
            'min_weight.min' => 'Khối lượng tối thiểu không được nhỏ hơn 0.',
            'max_weight.numeric' => 'Khối lượng tối đa phải là số.',
            'max_weight.min' => 'Khối lượng tối đa không được nhỏ hơn 0.',
            'max_weight.gte' => 'Khối lượng tối đa phải lớn hơn hoặc bằng khối lượng tối thiểu.',
            'markup_percent.numeric' => 'Tăng giá theo % phải là số.',
            'markup_percent.min' => 'Tăng giá theo % không được nhỏ hơn 0.',
            'markup_fixed.numeric' => 'Tăng giá cố định phải là số.',
            'markup_fixed.min' => 'Tăng giá cố định không được nhỏ hơn 0.',
            'min_fee.numeric' => 'Phí tối thiểu phải là số.',
            'min_fee.min' => 'Phí tối thiểu không được nhỏ hơn 0.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
