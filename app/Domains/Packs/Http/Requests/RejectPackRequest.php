<?php

namespace App\Domains\Packs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectPackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:1000']];
    }
}
