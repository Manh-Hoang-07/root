<?php

namespace App\Http\Requests\Admin\Menu;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('menu');
        return [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'route' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:menus,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên menu là bắt buộc.',
            'parent_id.exists' => 'Menu cha không hợp lệ.',
            'roles.array' => 'Danh sách quyền phải là một mảng.',
        ];
    }
}
