<?php

namespace App\Http\Requests\Core\File;

use Illuminate\Foundation\Http\FormRequest;

class UploadMultipleFilesRequest extends FormRequest
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
            'files' => 'required|array|min:1|max:10',
            'files.*' => 'required|file|max:512000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'files.required' => 'Vui lòng chọn ít nhất 1 file.',
            'files.array' => 'Files phải là mảng.',
            'files.min' => 'Vui lòng chọn ít nhất 1 file.',
            'files.max' => 'Không được upload quá 10 file cùng lúc.',
            'files.*.required' => 'Mỗi file là bắt buộc.',
            'files.*.file' => 'File không hợp lệ.',
            'files.*.max' => 'Kích thước file không được vượt quá 500MB.',
        ];
    }
}
