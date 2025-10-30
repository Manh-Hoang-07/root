<?php

namespace App\Http\Requests\Admin\Slider;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'image_path' => ['required', 'string', 'max:1024'],
            'link' => ['nullable', 'string', 'max:1024'],
            // Only allow active/inactive even if enum has extra states
            'status' => ['required', 'in:active,inactive'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
