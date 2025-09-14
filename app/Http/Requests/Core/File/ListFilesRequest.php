<?php

namespace App\Http\Requests\Core\File;

use Illuminate\Foundation\Http\FormRequest;

class ListFilesRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'type' => 'sometimes|string|max:50',
            'date' => 'sometimes|string|regex:/^\d{8}$/'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.string' => 'Type phải là chuỗi.',
            'type.max' => 'Type không được vượt quá 50 ký tự.',
            'date.string' => 'Date phải là chuỗi.',
            'date.regex' => 'Date phải có format yyyymmdd (ví dụ: 20241214).'
        ];
    }
}
