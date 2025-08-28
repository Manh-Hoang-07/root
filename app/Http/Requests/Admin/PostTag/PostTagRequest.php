<?php

namespace App\Http\Requests\Admin\PostTag;

use App\Enums\BasicStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('posttag');

        return [
            'name' => ['required','string','max:255'],
            'slug' => [
                'nullable','string','max:255',
                Rule::unique('posttag','slug')->ignore($id)
            ],
            'description' => ['nullable','string'],
            'status' => ['boolean'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:255'],
            'canonical_url' => ['nullable','string','max:1024'],
        ];
    }
}
