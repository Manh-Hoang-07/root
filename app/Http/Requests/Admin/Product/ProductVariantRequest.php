<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
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
        $rules = [
            'product_id' => 'required|integer|exists:products,id',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku,' . $this->route('id'),
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required_with:attributes|integer|exists:product_attributes,id',
            'attributes.*.value_id' => 'required_with:attributes|integer|exists:product_attribute_values,id',
        ];

        // For update requests, make some fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['product_id'] = 'sometimes|required|integer|exists:products,id';
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['price'] = 'sometimes|required|numeric|min:0';
            $rules['stock_quantity'] = 'sometimes|required|integer|min:0';
            $rules['status'] = 'sometimes|required|in:active,inactive';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Sản phẩm là bắt buộc',
            'product_id.exists' => 'Sản phẩm không tồn tại',
            'sku.unique' => 'Mã SKU đã tồn tại',
            'name.required' => 'Tên biến thể là bắt buộc',
            'name.max' => 'Tên biến thể không được vượt quá 255 ký tự',
            'price.required' => 'Giá là bắt buộc',
            'price.numeric' => 'Giá phải là số',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'stock_quantity.required' => 'Số lượng tồn kho là bắt buộc',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên',
            'stock_quantity.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'attributes.array' => 'Thuộc tính phải là mảng',
            'attributes.*.attribute_id.exists' => 'Thuộc tính không tồn tại',
            'attributes.*.value_id.exists' => 'Giá trị thuộc tính không tồn tại',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'status' => $this->status ?? 'active',
        ]);
    }
}
