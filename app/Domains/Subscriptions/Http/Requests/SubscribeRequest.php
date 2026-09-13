<?php

namespace App\Domains\Subscriptions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['creator_premium', 'extension_premium'])],
            'payment_method' => ['required', Rule::in(['stripe', 'paypal'])],
            'payment_token' => ['nullable', 'string'],
        ];
    }
}
