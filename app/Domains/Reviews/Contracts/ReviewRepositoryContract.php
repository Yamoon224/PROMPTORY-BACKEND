<?php

namespace App\Domains\Reviews\Contracts;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Review>
     */
    public function paginateForPrompt(int $promptId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findForUserAndPrompt(int $userId, int $promptId): ?Review;

    /** @param  array<string, mixed>  $attributes */
    public function upsert(int $userId, int $promptId, array $attributes): Review;

    public function delete(Review $review): void;
}
