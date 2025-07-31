<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ProductRequest extends FormRequest
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
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'image' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|string|in:active,inactive',
            'categories' => 'sometimes|array',
            'attributes' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
            
            // Variants validation
            'variants' => 'sometimes|array',
            'variants.*.sku' => 'required|string|max:100',
            'variants.*.barcode' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.image' => 'nullable|string|max:255',
            'variants.*.status' => 'required|string|in:active,inactive',
            'variants.*.attribute_values' => 'sometimes|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
            
            // Images validation
            'images' => 'sometimes|array',
            'images.*.url' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.max' => 'Tên sản phẩm không được vượt quá :max ký tự.',
            'description.required' => 'Mô tả không được để trống.',
            'short_description.required' => 'Mô tả ngắn không được để trống.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá :max ký tự.',
            'price.required' => 'Giá không được để trống.',
            'price.numeric' => 'Giá phải là số.',
            'price.min' => 'Giá không được âm.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được âm.',
            'image.required' => 'Ảnh không được để trống.',
            'image.max' => 'Đường dẫn ảnh không được vượt quá :max ký tự.',
            'weight.required' => 'Khối lượng không được để trống.',
            'weight.numeric' => 'Khối lượng phải là số.',
            'weight.min' => 'Khối lượng không được âm.',
            'length.required' => 'Chiều dài không được để trống.',
            'length.numeric' => 'Chiều dài phải là số.',
            'length.min' => 'Chiều dài không được âm.',
            'width.required' => 'Chiều rộng không được để trống.',
            'width.numeric' => 'Chiều rộng phải là số.',
            'width.min' => 'Chiều rộng không được âm.',
            'height.required' => 'Chiều cao không được để trống.',
            'height.numeric' => 'Chiều cao phải là số.',
            'height.min' => 'Chiều cao không được âm.',
            'brand_id.exists' => 'Thương hiệu không hợp lệ.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'categories.array' => 'Danh sách danh mục phải là mảng.',
            'categories.*.exists' => 'Danh mục không hợp lệ.',
            
            // Variants validation messages
            'variants.*.sku.required' => 'SKU của biến thể không được để trống.',
            'variants.*.sku.max' => 'SKU không được vượt quá :max ký tự.',
            'variants.*.barcode.max' => 'Barcode không được vượt quá :max ký tự.',
            'variants.*.price.required' => 'Giá biến thể không được để trống.',
            'variants.*.price.numeric' => 'Giá biến thể phải là số.',
            'variants.*.price.min' => 'Giá biến thể không được âm.',
            'variants.*.sale_price.numeric' => 'Giá khuyến mãi biến thể phải là số.',
            'variants.*.sale_price.min' => 'Giá khuyến mãi biến thể không được âm.',
            'variants.*.quantity.required' => 'Số lượng biến thể không được để trống.',
            'variants.*.quantity.integer' => 'Số lượng biến thể phải là số nguyên.',
            'variants.*.quantity.min' => 'Số lượng biến thể không được âm.',
            'variants.*.image.max' => 'Đường dẫn ảnh biến thể không được vượt quá :max ký tự.',
            'variants.*.status.required' => 'Trạng thái biến thể không được để trống.',
            'variants.*.status.in' => 'Trạng thái biến thể không hợp lệ.',
            'variants.*.attribute_values.*.exists' => 'Giá trị thuộc tính không hợp lệ.',
            
            // Images validation messages
            'images.*.url.required' => 'URL ảnh không được để trống.',
            'images.*.url.max' => 'URL ảnh không được vượt quá :max ký tự.',
        ];
    }


}
