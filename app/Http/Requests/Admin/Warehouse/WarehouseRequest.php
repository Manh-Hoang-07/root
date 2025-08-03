<?php

namespace App\Http\Requests\Admin\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
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
        $warehouseRoute = $this->route('warehouse');
        $warehouseId = is_object($warehouseRoute) ? $warehouseRoute->id : $warehouseRoute;

        return [
            'name' => 'required|string|max:255|unique:warehouses,name,' . $warehouseId,
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên kho không được để trống.',
            'name.max' => 'Tên kho không được vượt quá :max ký tự.',
            'name.unique' => 'Tên kho đã tồn tại.',
            'address.required' => 'Địa chỉ không được để trống.',
            'address.max' => 'Địa chỉ không được vượt quá :max ký tự.',
            'city.max' => 'Thành phố không được vượt quá :max ký tự.',
            'province.max' => 'Tỉnh/Thành không được vượt quá :max ký tự.',
            'manager_id.exists' => 'Người quản lý không hợp lệ.',
            'phone.max' => 'Số điện thoại không được vượt quá :max ký tự.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá :max ký tự.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
