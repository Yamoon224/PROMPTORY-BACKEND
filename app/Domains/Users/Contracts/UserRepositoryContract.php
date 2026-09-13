<?php

namespace App\Domains\Users\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): User;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): User;

    /** @param  array<string, mixed>  $attributes */
    public function update(User $user, array $attributes): User;

    public function delete(User $user): void;

    /** Nombre de ventes encaissees par ce createur : un compte qui a vendu ne se supprime pas. */
    public function countSales(User $user): int;
}
