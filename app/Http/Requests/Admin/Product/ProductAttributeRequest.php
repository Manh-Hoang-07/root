<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductAttributeRequest extends FormRequest
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
        $routeModel = $this->route('product_attribute');
        $routeId = $this->route('id');

        if ($routeModel instanceof \App\Models\ProductAttribute) {
            $id = $routeModel->id;
        } elseif (is_array($routeModel) && isset($routeModel['id'])) {
            $id = $routeModel['id'];
        } elseif (is_numeric($routeModel)) {
            $id = (int) $routeModel;
        } elseif (is_numeric($routeId)) {
            $id = (int) $routeId;
        }

        $slugRule = Rule::unique('product_attributes', 'slug');
        if ($id) {
            $slugRule = $slugRule->ignore($id);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'type' => 'required|in:text,textarea,select,multiselect,radio,checkbox,color,image',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'is_variant' => 'boolean',
            'is_filterable' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ];

        // For update requests, make some fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['type'] = 'sometimes|required|in:text,textarea,select,multiselect,radio,checkbox,color,image';
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
            'name.required' => 'Tên thuộc tính là bắt buộc',
            'name.max' => 'Tên thuộc tính không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug đã tồn tại',
            'type.required' => 'Loại thuộc tính là bắt buộc',
            'type.in' => 'Loại thuộc tính không hợp lệ',
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên',
            'sort_order.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0',
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
            'is_required' => $this->boolean('is_required', false),
            'is_variant' => $this->boolean('is_variant', false),
            'is_filterable' => $this->boolean('is_filterable', true),
            'sort_order' => $this->sort_order ?? 0,
        ]);
    }
}
