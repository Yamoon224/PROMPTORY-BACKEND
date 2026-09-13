<?php

namespace App\Domains\Sales\Contracts;

use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SaleRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Sale>
     */
    public function paginateForBuyer(int $buyerId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Sale>
     */
    public function paginateForCreator(int $creatorId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Sale>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByClientReference(string $reference): ?Sale;

    public function hasPurchasedPrompt(int $buyerId, int $promptId): bool;

    public function hasPurchasedPack(int $buyerId, int $packId): bool;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Sale;
}
