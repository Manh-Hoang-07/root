<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryRequest extends FormRequest
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
        $rules = [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:0',
            'batch_no' => 'nullable|string|max:100',
            'lot_no' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'reserved_quantity' => 'nullable|integer|min:0',
            'available_quantity' => 'nullable|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
        ];

        // Nếu là update, thêm unique rule cho product_id + warehouse_id + batch_no
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $inventoryId = $this->route('inventory');
            $rules['product_id'] .= '|unique:inventories,product_id,' . $inventoryId . ',id,warehouse_id,' . $this->warehouse_id . ',batch_no,' . ($this->batch_no ?? 'NULL');
        } else {
            // Nếu là create, kiểm tra unique
            $rules['product_id'] .= '|unique:inventories,product_id,NULL,id,warehouse_id,' . $this->warehouse_id . ',batch_no,' . ($this->batch_no ?? 'NULL');
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'product_id.unique' => 'Sản phẩm này đã có tồn kho trong kho hàng được chọn.',
            'warehouse_id.required' => 'Vui lòng chọn kho hàng.',
            'warehouse_id.exists' => 'Kho hàng không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng không được âm.',
            'batch_no.max' => 'Mã lô hàng không được vượt quá 100 ký tự.',
            'lot_no.max' => 'Số lô sản xuất không được vượt quá 100 ký tự.',
            'expiry_date.date' => 'Hạn sử dụng không đúng định dạng.',
            'expiry_date.after' => 'Hạn sử dụng phải sau ngày hiện tại.',
            'reserved_quantity.integer' => 'Số lượng đã giữ chỗ phải là số nguyên.',
            'reserved_quantity.min' => 'Số lượng đã giữ chỗ không được âm.',
            'available_quantity.integer' => 'Số lượng có thể bán phải là số nguyên.',
            'available_quantity.min' => 'Số lượng có thể bán không được âm.',
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

        if ($this->has('reserved_quantity')) {
            $this->merge([
                'reserved_quantity' => (int) $this->reserved_quantity
            ]);
        }

        if ($this->has('available_quantity')) {
            $this->merge([
                'available_quantity' => (int) $this->available_quantity
            ]);
        }

        if ($this->has('cost_price')) {
            $this->merge([
                'cost_price' => (float) $this->cost_price
            ]);
        }
    }
} 