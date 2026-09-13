<?php

namespace App\Domains\Auth\Http\Controllers;

use App\Domains\Auth\Http\Requests\ForgotPasswordRequest;
use App\Domains\Auth\Http\Requests\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Mot de passe oublie, via le broker de reinitialisation de Laravel
 * (table `password_reset_tokens`, deja presente dans le schema de base).
 *
 * En developpement (`MAIL_MAILER=log`), le lien de reinitialisation est ecrit
 * dans `storage/logs/laravel.log` plutot qu'envoye : c'est la un choix de
 * configuration d'environnement, pas du code applicatif a changer en
 * production.
 */
class PasswordResetController extends Controller
{
    /**
     * Toujours le meme message, que l'adresse corresponde a un compte ou non :
     * distinguer les deux transformerait ce formulaire en oracle
     * d'enumeration de comptes.
     */
    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => 'Si un compte existe pour cette adresse, un e-mail de reinitialisation vient de lui etre envoye.',
        ]);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill(['password' => $password])->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [$this->genericFailureMessage($status)],
            ]);
        }

        return response()->json(['message' => 'Mot de passe reinitialise. Vous pouvez vous connecter.']);
    }

    /**
     * Le statut brut de Laravel (`passwords.token`, `passwords.user`…) ne
     * doit jamais atteindre le client tel quel : il distinguerait un jeton
     * expire d'un compte inexistant, un detail qui ne regarde que le serveur.
     */
    private function genericFailureMessage(string $status): string
    {
        return Str::contains($status, 'throttled')
            ? 'Trop de tentatives. Patientez avant de reessayer.'
            : "Ce lien de reinitialisation n'est plus valide. Demandez-en un nouveau.";
    }
}
