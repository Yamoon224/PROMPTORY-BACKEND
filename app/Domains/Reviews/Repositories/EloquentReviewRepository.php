<?php

namespace App\Domains\Reviews\Repositories;

use App\Domains\Reviews\Contracts\ReviewRepositoryContract;
use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentReviewRepository implements ReviewRepositoryContract
{
    public function paginateForPrompt(int $promptId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Review::query()
            ->with('user:id,name')
            ->where('prompt_id', $promptId)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findForUserAndPrompt(int $userId, int $promptId): ?Review
    {
        return Review::query()->where('user_id', $userId)->where('prompt_id', $promptId)->first();
    }

    public function upsert(int $userId, int $promptId, array $attributes): Review
    {
        // Un seul avis par acheteur et par prompt (contrainte unique en base) :
        // republier son avis le remplace, il ne s'additionne pas.
        return Review::updateOrCreate(
            ['user_id' => $userId, 'prompt_id' => $promptId],
            $attributes,
        );
    }

    public function delete(Review $review): void
    {
        $review->delete();
    }
}
