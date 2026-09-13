<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Contracts\UserRepositoryContract;
use App\Domains\Users\Exceptions\UserNotDeletableException;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Administration des comptes.
 *
 * **Un compte qui a vendu ne se supprime pas.** Le supprimer laisserait des
 * ventes sans createur — precisement l'information qu'on cherche quand une
 * commission ne tombe pas juste en fin de mois.
 *
 * **Personne ne supprime son propre compte.** Un administrateur qui s'efface
 * par megarde laisse une plateforme sans administrateur.
 */
final class UserService
{
    public function __construct(private readonly UserRepositoryContract $users) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, User>
     */
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->users->paginate($filters, $perPage);
    }

    public function find(int $id): User
    {
        return $this->users->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $roles
     */
    public function create(array $data, array $roles): User
    {
        return DB::transaction(function () use ($data, $roles): User {
            $user = $this->users->create($data);
            $user->syncRoles($roles);

            return $user->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>|null  $roles  null laisse les roles inchanges
     */
    public function update(User $user, array $data, ?array $roles = null): User
    {
        return DB::transaction(function () use ($user, $data, $roles): User {
            // Un mot de passe vide dans un formulaire d'edition signifie « ne
            // pas changer », jamais « effacer ».
            if (($data['password'] ?? null) === null) {
                unset($data['password']);
            }

            $updated = $this->users->update($user, $data);

            if ($roles !== null) {
                $updated->syncRoles($roles);
            }

            return $updated->refresh();
        });
    }

    /** @throws UserNotDeletableException */
    public function delete(User $user, ?int $currentUserId = null): void
    {
        if ($currentUserId !== null && $user->id === $currentUserId) {
            throw UserNotDeletableException::self();
        }

        $sales = $this->users->countSales($user);

        if ($sales > 0) {
            throw UserNotDeletableException::hasSales($user->name, $sales);
        }

        $this->users->delete($user);
    }
}
