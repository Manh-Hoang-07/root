<?php

namespace App\Http\Requests\Admin\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class ShippingDeliverySettingRequest extends FormRequest
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
        $settingId = $this->route('delivery') ? $this->route('delivery')->id : null;

        return [
            'key' => 'required|string|max:255|unique:shipping_delivery_settings,key,' . $settingId,
            'value' => 'required|string',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'Key không được để trống.',
            'key.max' => 'Key không được vượt quá :max ký tự.',
            'key.unique' => 'Key đã tồn tại.',
            'value.required' => 'Giá trị không được để trống.',
            'description.string' => 'Mô tả phải là chuỗi.',
        ];
    }
}
