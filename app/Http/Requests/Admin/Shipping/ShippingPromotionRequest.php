<?php

namespace App\Http\Requests\Admin\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class ShippingPromotionRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'discount' => 'required|numeric',
            'type' => 'required|string|max:50',
            'service_id' => 'nullable|exists:shipping_services,id',
            'zone_id' => 'nullable|exists:shipping_zones,id',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'status' => 'required|string|max:50',
        ];
    }
}
