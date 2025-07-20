<?php

namespace App\Http\Requests\Admin\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class ShippingPricingRuleRequest extends FormRequest
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
        return [
            'service_id' => 'required|exists:shipping_services,id',
            'zone_id' => 'required|exists:shipping_zones,id',
            'min_weight' => 'nullable|numeric',
            'max_weight' => 'nullable|numeric',
            'markup_percent' => 'nullable|numeric',
            'markup_fixed' => 'nullable|numeric',
            'min_fee' => 'nullable|numeric',
            'status' => 'required|string|max:50',
        ];
    }
}
