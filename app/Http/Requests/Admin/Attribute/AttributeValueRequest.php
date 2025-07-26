<?php

namespace App\Http\Requests\Admin\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class AttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $attributeValueRoute = $this->route('attributeValue');
        $attributeValueId = is_object($attributeValueRoute) ? $attributeValueRoute->id : $attributeValueRoute;

        return [
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:100',
            'name' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'attribute_id.required' => 'Vui lòng chọn thuộc tính.',
            'attribute_id.exists' => 'Thuộc tính không hợp lệ.',
            'value.required' => 'Giá trị không được để trống.',
            'value.max' => 'Giá trị không được vượt quá :max ký tự.',
            'name.max' => 'Tên không được vượt quá :max ký tự.',
            'sort_order.required' => 'Thứ tự sắp xếp không được để trống.',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên.',
            'sort_order.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'description.string' => 'Mô tả phải là chuỗi.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        // Chuyển các trường có giá trị 'null' (string) thành null thực sự
        foreach (['name', 'description'] as $field) {
            if (isset($data[$field]) && $data[$field] === 'null') {
                $data[$field] = null;
            }
        }
        $allowed = [
            'attribute_id',
            'value',
            'name',
            'sort_order',
            'status',
            'description',
        ];
        return array_intersect_key($data, array_flip($allowed));
    }
}
