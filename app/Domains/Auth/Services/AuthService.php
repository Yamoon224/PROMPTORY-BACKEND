<?php

namespace App\Domains\Auth\Services;

use App\Domains\Users\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Authentification par jeton Sanctum.
 *
 * Le frontend Next.js et l'extension Chrome sont sur des origines distinctes
 * sans session partagee : le mode cookie/SPA ne s'applique pas ici, et le
 * client envoie `Authorization: Bearer {token}`.
 */
final class AuthService
{
    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array{user: User, token: string}
     *
     * @throws ValidationException
     */
    public function attempt(array $credentials, string $deviceName = 'web'): array
    {
        if (! Auth::validate($credentials)) {
            // Message volontairement identique que le compte existe ou non :
            // distinguer les deux cas transformerait le formulaire en oracle
            // d'enumeration de comptes.
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }

        /** @var User $user */
        $user = User::where('email', $credentials['email'])->firstOrFail();

        if ($user->status === UserStatus::Inactive) {
            throw ValidationException::withMessages([
                'email' => ['Ce compte est desactive. Contactez le support.'],
            ]);
        }

        return [
            'user' => $user,
            'token' => $user->createToken($deviceName)->plainTextToken,
        ];
    }

    /**
     * Inscription publique.
     *
     * Le role est impose ici et jamais lu depuis la requete : une inscription
     * publique qui accepterait un role permettrait a n'importe qui de se
     * declarer administrateur.
     *
     * @param  array{name: string, email: string, password: string}  $data
     * @return array{user: User, token: string}
     */
    public function register(array $data, string $deviceName = 'web'): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'user',
            'status' => 'active',
        ]);

        $user->assignRole('user');

        return [
            'user' => $user->refresh(),
            'token' => $user->createToken($deviceName)->plainTextToken,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
