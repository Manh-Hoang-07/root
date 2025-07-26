<?php

namespace App\Http\Requests\Admin\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $attributeRoute = $this->route('attribute');
        $attributeId = is_object($attributeRoute) ? $attributeRoute->id : $attributeRoute;

        return [
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:text,number,select,color',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên thuộc tính không được để trống.',
            'name.max' => 'Tên thuộc tính không được vượt quá :max ký tự.',
            'name.unique' => 'Tên thuộc tính đã tồn tại.',
            'type.required' => 'Kiểu thuộc tính không được để trống.',
            'type.in' => 'Kiểu thuộc tính không hợp lệ.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'description.string' => 'Mô tả phải là chuỗi.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        // Chuyển các trường có giá trị 'null' (string) thành null thực sự
        if (isset($data['description']) && $data['description'] === 'null') {
            $data['description'] = null;
        }
        $allowed = [
            'name',
            'type',
            'status',
            'description',
        ];
        return array_intersect_key($data, array_flip($allowed));
    }
}
