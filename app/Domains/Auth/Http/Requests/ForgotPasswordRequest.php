<?php

namespace App\Domains\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        // Volontairement sans `exists:users,email` : valider l'existence du
        // compte ici transformerait ce formulaire en oracle d'enumeration de
        // comptes (« cette adresse n'est associee a aucun compte » revele deja
        // beaucoup). Le controleur repond le meme message dans tous les cas.
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }
}
