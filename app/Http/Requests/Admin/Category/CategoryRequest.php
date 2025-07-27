<?php
namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        Log::info('Request all:', $this->all());
        $categoryRoute = $this->route('category');
        $categoryId = is_object($categoryRoute) ? $categoryRoute->id : $categoryRoute;

        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:10000',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.max' => 'Tên danh mục không được vượt quá :max ký tự.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'image.string' => 'Ảnh phải là chuỗi.',
            'description.string' => 'Mô tả phải là chuỗi.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        Log::info('Validated data:', $data);
        // Chuyển các trường có giá trị 'null' (string) thành null thực sự
        foreach (['parent_id', 'image'] as $field) {
            if (isset($data[$field]) && $data[$field] === 'null') {
                $data[$field] = null;
            }
        }
        $allowed = [
            'name',
            'parent_id',
            'description',
            'status',
            'image',
        ];
        return array_intersect_key($data, array_flip($allowed));
    }
} 