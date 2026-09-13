<?php

namespace App\Domains\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchasePromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            // Jeton fourni par le SDK du prestataire (Stripe.js…), jamais un numero de carte.
            'payment_token' => ['nullable', 'string'],
            // Rejoue une requete sans debiter deux fois (double clic, coupure reseau).
            'client_reference' => ['nullable', 'string', 'max:255'],
        ];
    }
}
