<?php

namespace App\Http\Requests\Admin\Variant;

use Illuminate\Foundation\Http\FormRequest;

class VariantRequest extends FormRequest
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
        $variantId = $this->route('variant') ? $this->route('variant')->id : null;

        return [
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|string|max:100|unique:variants,sku,' . $variantId,
            'barcode' => 'nullable|string|max:100|unique:variants,barcode,' . $variantId,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'attribute_values' => 'sometimes|array',
            'attribute_values.*' => 'exists:attribute_values,id',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Sản phẩm là bắt buộc.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'sku.required' => 'SKU là bắt buộc.',
            'sku.max' => 'SKU không được vượt quá :max ký tự.',
            'sku.unique' => 'SKU đã tồn tại.',
            'barcode.max' => 'Barcode không được vượt quá :max ký tự.',
            'barcode.unique' => 'Barcode đã tồn tại.',
            'price.required' => 'Giá là bắt buộc.',
            'price.numeric' => 'Giá phải là số.',
            'price.min' => 'Giá không được âm.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được âm.',
            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng không được âm.',
            'image.max' => 'Đường dẫn ảnh không được vượt quá :max ký tự.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'attribute_values.array' => 'Danh sách thuộc tính phải là mảng.',
            'attribute_values.*.exists' => 'Giá trị thuộc tính không tồn tại.',
        ];
    }
} 