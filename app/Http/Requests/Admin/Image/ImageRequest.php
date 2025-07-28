<?php

namespace App\Http\Requests\Admin\Image;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
            'url' => 'required|string|max:500',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0',
            'imageable_type' => 'required|string',
            'imageable_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'URL ảnh là bắt buộc.',
            'url.max' => 'URL ảnh không được vượt quá :max ký tự.',
            'alt_text.max' => 'Mô tả ảnh không được vượt quá :max ký tự.',
            'caption.max' => 'Chú thích không được vượt quá :max ký tự.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
            'order.min' => 'Thứ tự không được âm.',
            'imageable_type.required' => 'Loại đối tượng là bắt buộc.',
            'imageable_id.required' => 'ID đối tượng là bắt buộc.',
            'imageable_id.integer' => 'ID đối tượng phải là số.',
        ];
    }
} 