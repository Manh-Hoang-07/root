<?php

namespace App\Http\Requests\Public\Contact;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'subject' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:5000'],
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
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            
            'subject.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'subject.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            
            'content.required' => 'Nội dung là bắt buộc.',
            'content.string' => 'Nội dung phải là chuỗi ký tự.',
            'content.min' => 'Nội dung phải có ít nhất 10 ký tự.',
            'content.max' => 'Nội dung không được vượt quá 5000 ký tự.',
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
            'name' => 'tên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'subject' => 'tiêu đề',
            'content' => 'nội dung',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s()]/', '', $this->phone)
            ]);
        }

        // Trim string fields
        $this->merge([
            'name' => trim($this->name ?? ''),
            'email' => trim($this->email ?? ''),
            'subject' => trim($this->subject ?? ''),
            'content' => trim($this->content ?? ''),
        ]);
    }
}
