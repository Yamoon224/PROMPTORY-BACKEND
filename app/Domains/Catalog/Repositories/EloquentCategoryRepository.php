<?php

namespace App\Domains\Catalog\Repositories;

use App\Domains\Catalog\Contracts\CategoryRepositoryContract;
use App\Domains\Shared\Support\Sort;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentCategoryRepository implements CategoryRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = ['name' => 'name', 'created_at' => 'created_at'];

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Category::query()
            ->withCount('prompts')
            ->with('parent:id,name,slug')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($filters['parent_id'] ?? null, fn ($query, $id) => $query->where('parent_id', $id))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'name', 'asc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function all(): array
    {
        return Category::query()->orderBy('name')->get()->all();
    }

    public function findOrFail(int $id): Category
    {
        return Category::query()->with('parent')->findOrFail($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::query()->where('slug', $slug)->first();
    }

    public function create(array $attributes): Category
    {
        return Category::create($attributes);
    }

    public function update(Category $category, array $attributes): Category
    {
        $category->update($attributes);

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function countDependents(Category $category): int
    {
        return $category->prompts()->count() + $category->children()->count();
    }
}
