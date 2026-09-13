<?php

namespace App\Domains\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            // Moyen de paiement choisi par l'acheteur au moment de payer.
            'payment_method' => ['required', Rule::in(['stripe', 'paypal'])],
            // Jeton fourni par le SDK du prestataire (Stripe.js, PayPal JS SDK…), jamais un numero de carte.
            'payment_token' => ['nullable', 'string'],
            // Rejoue une requete sans debiter deux fois (double clic, coupure reseau).
            'client_reference' => ['nullable', 'string', 'max:255'],
        ];
    }
}
