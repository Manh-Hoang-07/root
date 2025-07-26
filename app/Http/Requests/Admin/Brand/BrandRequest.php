<?php

namespace App\Http\Requests\Admin\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandRoute = $this->route('brand');
        $brandId = is_object($brandRoute) ? $brandRoute->id : $brandRoute;

        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên thương hiệu không được để trống.',
            'name.max' => 'Tên thương hiệu không được vượt quá :max ký tự.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'image.string' => 'Ảnh phải là chuỗi.',
            'description.string' => 'Mô tả phải là chuỗi.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        // Chuyển các trường có giá trị 'null' (string) thành null thực sự
        if (isset($data['image']) && $data['image'] === 'null') {
            $data['image'] = null;
        }
        $allowed = [
            'name',
            'description',
            'status',
            'image',
        ];
        return array_intersect_key($data, array_flip($allowed));
    }
}
