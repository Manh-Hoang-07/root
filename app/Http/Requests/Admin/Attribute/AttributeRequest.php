<?php

namespace App\Http\Requests\Admin\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
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
        $attributeId = $this->route('attribute') ? $this->route('attribute')->id : null;

        return [
            'name' => 'required|string|max:255|unique:attributes,name,' . $attributeId,
            'type' => 'required|string|max:255',
            'status' => 'sometimes|string|in:active,inactive',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên thuộc tính không được để trống.',
            'name.max' => 'Tên thuộc tính không được vượt quá :max ký tự.',
            'name.unique' => 'Tên thuộc tính đã tồn tại.',
            'type.required' => 'Kiểu thuộc tính không được để trống.',
            'type.max' => 'Kiểu thuộc tính không được vượt quá :max ký tự.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'description.string' => 'Mô tả phải là chuỗi.',
        ];
    }
}
