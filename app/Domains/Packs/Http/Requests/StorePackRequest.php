<?php

namespace App\Domains\Packs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'prompts' => ['required', 'array', 'min:1'],
            'prompts.*' => ['integer', 'exists:prompts,id'],
        ];
    }
}
