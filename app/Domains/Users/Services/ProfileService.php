<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Contracts\UserRepositoryContract;
use App\Domains\Users\Exceptions\InvalidCurrentPasswordException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Gestion du compte par son propre titulaire : nom, e-mail, mot de passe.
 *
 * Distinct de `UserService` (administration de n'importe quel compte par le
 * back-office) : un utilisateur ne peut changer ni son role ni son statut en
 * modifiant son propre profil, une distinction que deux services separes
 * rendent impossible a contourner par erreur de branchement de route.
 */
final class ProfileService
{
    public function __construct(private readonly UserRepositoryContract $users) {}

    /** @param  array{name?: string, email?: string}  $data */
    public function updateProfile(User $user, array $data): User
    {
        return $this->users->update($user, $data);
    }

    /** @throws InvalidCurrentPasswordException */
    public function updatePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw InvalidCurrentPasswordException::make();
        }

        $this->users->update($user, ['password' => $newPassword]);
    }
}
