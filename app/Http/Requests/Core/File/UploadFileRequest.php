<?php

namespace App\Http\Requests\Core\File;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
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
            'file' => 'required|file|max:512000', // 500MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn file.',
            'file.file' => 'File không hợp lệ.',
            'file.max' => 'Kích thước file không được vượt quá 500MB.',
        ];
    }
}
