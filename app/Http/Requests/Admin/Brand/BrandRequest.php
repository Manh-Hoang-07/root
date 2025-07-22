<?php

namespace App\Http\Requests\Admin\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
        $brandId = $this->route('brand') ? $this->route('brand')->id : null;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,' . $brandId,
            'image' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên thương hiệu không được để trống.',
            'name.max' => 'Tên thương hiệu không được vượt quá :max ký tự.',
            'slug.required' => 'Slug không được để trống.',
            'slug.max' => 'Slug không được vượt quá :max ký tự.',
            'slug.unique' => 'Slug đã tồn tại.',
            'image.required' => 'Ảnh không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'description.string' => 'Mô tả phải là chuỗi.',
        ];
    }
}
