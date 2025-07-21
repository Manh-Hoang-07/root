<?php

namespace App\Http\Requests\Admin\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class ShippingApiConfigRequest extends FormRequest
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
        $apiConfigId = $this->route('api') ? $this->route('api')->id : null;

        return [
            'name' => 'required|string|max:255|unique:shipping_api_configs,name,' . $apiConfigId,
            'api_key' => 'required|string|max:255',
            'secret_key' => 'nullable|string|max:255',
            'env' => 'nullable|string|in:production,test',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên cấu hình không được để trống.',
            'name.max' => 'Tên cấu hình không được vượt quá :max ký tự.',
            'name.unique' => 'Tên cấu hình đã tồn tại.',
            'api_key.required' => 'API Key không được để trống.',
            'api_key.max' => 'API Key không được vượt quá :max ký tự.',
            'secret_key.max' => 'Secret Key không được vượt quá :max ký tự.',
            'env.in' => 'Môi trường không hợp lệ.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
