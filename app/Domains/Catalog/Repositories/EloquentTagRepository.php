<?php

namespace App\Domains\Catalog\Repositories;

use App\Domains\Catalog\Contracts\TagRepositoryContract;
use App\Domains\Shared\Support\Sort;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentTagRepository implements TagRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = ['name' => 'name', 'created_at' => 'created_at'];

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Tag::query()
            ->withCount('prompts')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'name', 'asc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function all(): array
    {
        return Tag::query()->orderBy('name')->get()->all();
    }

    public function findOrFail(int $id): Tag
    {
        return Tag::query()->findOrFail($id);
    }

    public function create(array $attributes): Tag
    {
        return Tag::create($attributes);
    }

    public function update(Tag $tag, array $attributes): Tag
    {
        $tag->update($attributes);

        return $tag->refresh();
    }

    public function delete(Tag $tag): void
    {
        $tag->delete();
    }

    public function countDependents(Tag $tag): int
    {
        return $tag->prompts()->count();
    }
}
