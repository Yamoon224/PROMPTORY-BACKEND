<?php

namespace App\Domains\Packs\Contracts;

use App\Models\Pack;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PackRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Pack>
     */
    public function paginatePublished(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Pack>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Pack>
     */
    public function paginatePendingValidation(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Pack;

    public function findBySlug(string $slug): Pack;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Pack;

    /** @param  array<string, mixed>  $attributes */
    public function update(Pack $pack, array $attributes): Pack;

    public function delete(Pack $pack): void;

    /** @param  list<int>  $promptIds */
    public function syncPrompts(Pack $pack, array $promptIds): void;
}
