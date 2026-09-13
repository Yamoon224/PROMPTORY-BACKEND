<?php

namespace App\Domains\Catalog\Contracts;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TagRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Tag>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /** @return list<Tag> */
    public function all(): array;

    public function findOrFail(int $id): Tag;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Tag;

    /** @param  array<string, mixed>  $attributes */
    public function update(Tag $tag, array $attributes): Tag;

    public function delete(Tag $tag): void;

    public function countDependents(Tag $tag): int;
}
