<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ExportInventoryRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'batch_no' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'warehouse_id.required' => 'Vui lòng chọn kho hàng.',
            'warehouse_id.exists' => 'Kho hàng không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng xuất kho.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng xuất kho phải lớn hơn 0.',
            'batch_no.max' => 'Mã lô hàng không được vượt quá 100 ký tự.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Chuyển đổi dữ liệu nếu cần
        if ($this->has('quantity')) {
            $this->merge([
                'quantity' => (int) $this->quantity
            ]);
        }
    }
} 