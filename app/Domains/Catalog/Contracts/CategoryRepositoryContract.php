<?php

namespace App\Domains\Catalog\Contracts;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CategoryRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Category>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Toutes les categories, pour alimenter les selecteurs de la marketplace.
     *
     * @return list<Category>
     */
    public function all(): array;

    public function findOrFail(int $id): Category;

    public function findBySlug(string $slug): ?Category;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Category;

    /** @param  array<string, mixed>  $attributes */
    public function update(Category $category, array $attributes): Category;

    public function delete(Category $category): void;

    public function countDependents(Category $category): int;
}
