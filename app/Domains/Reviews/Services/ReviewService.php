<?php

namespace App\Domains\Reviews\Services;

use App\Domains\Reviews\Contracts\ReviewRepositoryContract;
use App\Domains\Reviews\Exceptions\ReviewNotAllowedException;
use App\Domains\Sales\Contracts\SaleRepositoryContract;
use App\Domains\Shared\Exceptions\OwnershipViolationException;
use App\Models\Prompt;
use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ReviewService
{
    public function __construct(
        private readonly ReviewRepositoryContract $reviews,
        private readonly SaleRepositoryContract $sales,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Review>
     */
    public function forPrompt(int $promptId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->reviews->paginateForPrompt($promptId, $filters, $perPage);
    }

    /**
     * @throws ReviewNotAllowedException
     */
    public function submit(int $userId, Prompt $prompt, int $rating, ?string $comment): Review
    {
        if ($prompt->user_id === $userId) {
            throw ReviewNotAllowedException::ownPrompt();
        }

        // Un prompt gratuit se telecharge sans achat enregistre : le noter
        // reste ouvert a quiconque, seule la vente conditionne l'avis d'un
        // prompt payant.
        if ((float) $prompt->price > 0 && ! $this->sales->hasPurchasedPrompt($userId, $prompt->id)) {
            throw ReviewNotAllowedException::notPurchased();
        }

        return $this->reviews->upsert($userId, $prompt->id, ['rating' => $rating, 'comment' => $comment]);
    }

    /** @throws OwnershipViolationException */
    public function delete(Review $review, int $userId): void
    {
        if ($review->user_id !== $userId) {
            throw OwnershipViolationException::make();
        }

        $this->reviews->delete($review);
    }
}
