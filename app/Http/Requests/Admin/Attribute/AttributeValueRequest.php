<?php

namespace App\Http\Requests\Admin\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class AttributeValueRequest extends FormRequest
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
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'sort_order' => 'required|integer',
            'status' => 'sometimes|string|in:active,inactive',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'attribute_id.required' => 'Vui lòng chọn thuộc tính.',
            'attribute_id.exists' => 'Thuộc tính không hợp lệ.',
            'value.required' => 'Giá trị không được để trống.',
            'value.max' => 'Giá trị không được vượt quá :max ký tự.',
            'name.max' => 'Tên không được vượt quá :max ký tự.',
            'sort_order.required' => 'Thứ tự sắp xếp không được để trống.',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'description.string' => 'Mô tả phải là chuỗi.',
        ];
    }
}
