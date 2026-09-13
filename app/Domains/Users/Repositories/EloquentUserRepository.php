<?php

namespace App\Domains\Users\Repositories;

use App\Domains\Shared\Support\Sort;
use App\Domains\Users\Contracts\UserRepositoryContract;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentUserRepository implements UserRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = [
        'name' => 'name',
        'email' => 'email',
        'status' => 'status',
        'created_at' => 'created_at',
    ];

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->with('roles:id,name')
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->whereHas(
                'roles',
                fn ($roles) => $roles->where('name', $role),
            ))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(
                fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"),
            ))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'name', 'asc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findOrFail(int $id): User
    {
        return User::query()->with('roles')->findOrFail($id);
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function countSales(User $user): int
    {
        return $user->salesAsCreator()->count();
    }
}
