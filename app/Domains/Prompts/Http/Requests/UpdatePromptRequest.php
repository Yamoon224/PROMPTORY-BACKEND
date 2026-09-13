<?php

namespace App\Domains\Prompts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:999.99'],
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'ia_models' => ['sometimes', 'array'],
            'ia_models.*' => ['integer', 'exists:ia_models,id'],
        ];
    }
}
