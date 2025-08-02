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
            'description' => 'nullable|string',
            'type' => 'required|string|in:text,textarea,select,multiselect,boolean,date,datetime',
            'is_required' => 'boolean',
            'is_unique' => 'boolean',
            'is_filterable' => 'boolean',
            'is_searchable' => 'boolean',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên thuộc tính là bắt buộc.',
            'name.max' => 'Tên thuộc tính không được vượt quá :max ký tự.',
            'type.required' => 'Loại thuộc tính là bắt buộc.',
            'type.in' => 'Loại thuộc tính không hợp lệ.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
