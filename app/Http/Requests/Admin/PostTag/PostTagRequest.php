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
            'description' => ['nullable','string'],
            'status' => ['string'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:255'],
            'canonical_url' => ['nullable','string','max:1024'],
        ];
    }
}
