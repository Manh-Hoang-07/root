<?php

namespace App\Http\Requests\Admin\PostCategory;

use App\Enums\BasicStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('postcategory');

        return [
            'name' => ['required','string','max:255'],
            'slug' => [
                'nullable','string','max:255',
                Rule::unique('postcategory','slug')->ignore($id)
            ],
            'description' => ['nullable','string'],
            'parent_id' => ['nullable','integer','exists:postcategory,id'],
            'image' => ['nullable','string','max:1024'],
            'status' => ['boolean'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:255'],
            'canonical_url' => ['nullable','string','max:1024'],
            'og_image' => ['nullable','string','max:1024'],
            'sort_order' => ['nullable','integer'],
        ];
    }
}
