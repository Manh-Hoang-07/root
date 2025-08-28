<?php

namespace App\Http\Requests\Admin\Post;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postId = $this->route('post');

        return [
            'name' => ['required','string','max:255'],
            'slug' => [
                'nullable','string','max:255',
                Rule::unique('posts','slug')->ignore($postId)
            ],
            'excerpt' => ['nullable','string'],
            'content' => ['required','string'],
            'image' => ['nullable','string','max:1024'],
            'cover_image' => ['nullable','string','max:1024'],
            'primary_postcategory_id' => ['nullable','integer','exists:postcategory,id'],
            'status' => ['required', Rule::in(PostStatus::values())],
            'is_featured' => ['boolean'],
            'is_pinned' => ['boolean'],
            'published_at' => ['nullable','date'],
            'view_count' => ['nullable','integer','min:0'],
            'meta_title' => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:255'],
            'canonical_url' => ['nullable','string','max:1024'],
            'og_title' => ['nullable','string','max:255'],
            'og_description' => ['nullable','string','max:255'],
            'og_image' => ['nullable','string','max:1024'],

            // Relations (optional)
            'tag_ids' => ['sometimes','array'],
            'tag_ids.*' => ['integer','exists:posttag,id'],
            'category_ids' => ['sometimes','array'],
            'category_ids.*' => ['integer','exists:postcategory,id'],
        ];
    }
}
