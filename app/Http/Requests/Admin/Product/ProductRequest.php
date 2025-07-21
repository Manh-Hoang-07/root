<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $productId,
            'description' => 'required|string',
            'short_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'image' => 'required|string',
            'weight' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|string|in:active,inactive',
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.max' => 'Tên sản phẩm không được vượt quá :max ký tự.',
            'slug.required' => 'Slug không được để trống.',
            'slug.max' => 'Slug không được vượt quá :max ký tự.',
            'slug.unique' => 'Slug đã tồn tại.',
            'description.required' => 'Mô tả không được để trống.',
            'short_description.required' => 'Mô tả ngắn không được để trống.',
            'price.required' => 'Giá không được để trống.',
            'price.numeric' => 'Giá phải là số.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'image.required' => 'Ảnh không được để trống.',
            'weight.required' => 'Khối lượng không được để trống.',
            'weight.numeric' => 'Khối lượng phải là số.',
            'length.required' => 'Chiều dài không được để trống.',
            'length.numeric' => 'Chiều dài phải là số.',
            'width.required' => 'Chiều rộng không được để trống.',
            'width.numeric' => 'Chiều rộng phải là số.',
            'height.required' => 'Chiều cao không được để trống.',
            'height.numeric' => 'Chiều cao phải là số.',
            'brand_id.exists' => 'Thương hiệu không hợp lệ.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'categories.array' => 'Danh sách danh mục phải là mảng.',
            'categories.*.exists' => 'Danh mục không hợp lệ.',
        ];
    }
}
