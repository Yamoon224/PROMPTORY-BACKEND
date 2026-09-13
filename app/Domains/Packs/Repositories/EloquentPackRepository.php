<?php

namespace App\Domains\Packs\Repositories;

use App\Domains\Packs\Contracts\PackRepositoryContract;
use App\Domains\Packs\Enums\PackStatus;
use App\Domains\Shared\Support\Sort;
use App\Models\Pack;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class EloquentPackRepository implements PackRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = ['title' => 'title', 'price' => 'price', 'created_at' => 'created_at'];

    public function paginatePublished(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->where('status', PackStatus::Published)
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->where('user_id', $userId)
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginatePendingValidation(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->where('status', PackStatus::PendingValidation)
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'asc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findOrFail(int $id): Pack
    {
        return Pack::query()->with(['user:id,name', 'prompts'])->findOrFail($id);
    }

    public function findBySlug(string $slug): Pack
    {
        return Pack::query()->with(['user:id,name', 'prompts'])->where('slug', $slug)->firstOrFail();
    }

    public function create(array $attributes): Pack
    {
        return Pack::create($attributes);
    }

    public function update(Pack $pack, array $attributes): Pack
    {
        $pack->update($attributes);

        return $pack->refresh();
    }

    public function delete(Pack $pack): void
    {
        $pack->delete();
    }

    public function syncPrompts(Pack $pack, array $promptIds): void
    {
        $pack->prompts()->sync($promptIds);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Pack>
     */
    private function baseQuery(array $filters): Builder
    {
        return Pack::query()
            ->with(['user:id,name'])
            ->withCount('prompts')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"));
    }
}
