<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ImportInventoryRequest extends FormRequest
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
            'lot_no' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'cost_price' => 'nullable|numeric|min:0',
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
            'quantity.required' => 'Vui lòng nhập số lượng nhập kho.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng nhập kho phải lớn hơn 0.',
            'batch_no.max' => 'Mã lô hàng không được vượt quá 100 ký tự.',
            'lot_no.max' => 'Số lô sản xuất không được vượt quá 100 ký tự.',
            'expiry_date.date' => 'Hạn sử dụng không đúng định dạng.',
            'expiry_date.after' => 'Hạn sử dụng phải sau ngày hiện tại.',
            'cost_price.numeric' => 'Giá vốn phải là số.',
            'cost_price.min' => 'Giá vốn không được âm.',
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

        if ($this->has('cost_price')) {
            $this->merge([
                'cost_price' => (float) $this->cost_price
            ]);
        }
    }
} 