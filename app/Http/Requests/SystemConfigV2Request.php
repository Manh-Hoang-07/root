<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemConfigV2Request extends FormRequest
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
            'key' => 'required|string|max:255',
            'value' => 'nullable',
            'group' => 'nullable|string|max:100',
            'is_public' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
            'type' => 'nullable|string|in:string,number,boolean,json,text'
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'Key là bắt buộc',
            'key.string' => 'Key phải là chuỗi',
            'key.max' => 'Key không được quá 255 ký tự',
            'group.string' => 'Group phải là chuỗi',
            'group.max' => 'Group không được quá 100 ký tự',
            'is_public.boolean' => 'Is public phải là boolean',
            'description.string' => 'Description phải là chuỗi',
            'description.max' => 'Description không được quá 500 ký tự',
            'type.in' => 'Type phải là một trong: string, number, boolean, json, text'
        ];
    }
}
