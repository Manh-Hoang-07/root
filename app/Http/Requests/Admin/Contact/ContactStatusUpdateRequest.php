<?php

namespace App\Http\Requests\Admin\Contact;

use App\Enums\ContactStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactStatusUpdateRequest extends FormRequest
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
            'status' => ['required', Rule::enum(ContactStatus::class)],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.enum' => 'Trạng thái không hợp lệ.',
            
            'admin_notes.string' => 'Ghi chú admin phải là chuỗi ký tự.',
            'admin_notes.max' => 'Ghi chú admin không được vượt quá 2000 ký tự.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'trạng thái',
            'admin_notes' => 'ghi chú admin',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim admin_notes
        $this->merge([
            'admin_notes' => trim($this->admin_notes ?? ''),
        ]);
    }
}
