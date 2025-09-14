<?php

namespace App\Http\Requests\Core\File;

use Illuminate\Foundation\Http\FormRequest;

class GetFileInfoRequest extends FormRequest
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
            'path' => 'required|string'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'path.required' => 'Path là bắt buộc.',
            'path.string' => 'Path phải là chuỗi.'
        ];
    }
}
