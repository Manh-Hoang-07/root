<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        // Detect current record id from route parameters (supports implicit binding or numeric id)
        $id = null;
        $routeProduct = $this->route('product');
        $routeId = $this->route('id');

        if ($routeProduct instanceof \App\Models\Product) {
            $id = $routeProduct->id;
        } elseif (is_array($routeProduct) && isset($routeProduct['id'])) {
            $id = $routeProduct['id'];
        } elseif (is_numeric($routeProduct)) {
            $id = (int) $routeProduct;
        } elseif (is_numeric($routeId)) {
            $id = (int) $routeId;
        }

        $slugRule = Rule::unique('products', 'slug');
        $skuRule = Rule::unique('products', 'sku');

        if ($id) {
            $slugRule = $slugRule->ignore($id);
            $skuRule = $skuRule->ignore($id);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'sku' => ['required', 'string', 'max:100', $skuRule],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'min_stock_level' => 'nullable|integer|min:0',
            'image' => 'nullable|string|max:500',
            'gallery' => 'nullable|array',
            'gallery.*' => 'string|max:500',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'boolean',
            'is_variable' => 'boolean',
            'is_digital' => 'boolean',
            'download_limit' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:product_categories,id',
        ];

        // For update requests, make some fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['sku'] = ['sometimes', 'required', 'string', 'max:100', $skuRule];
            $rules['status'] = 'sometimes|required|in:active,inactive,draft';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug đã tồn tại',
            'sku.required' => 'Mã SKU là bắt buộc',
            'sku.unique' => 'Mã SKU đã tồn tại',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'category_ids.array' => 'Danh mục phải là mảng',
            'category_ids.*.exists' => 'Danh mục không tồn tại',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug if not provided
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name)
            ]);
        }

        // Set default values
        $this->merge([
            'is_featured' => $this->boolean('is_featured', false),
            'is_variable' => $this->boolean('is_variable', false),
            'is_digital' => $this->boolean('is_digital', false),
        ]);
    }
}
