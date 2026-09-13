<?php

namespace App\Domains\Prompts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectPromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
