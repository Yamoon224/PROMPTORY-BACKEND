<?php

namespace App\Domains\Sales\Repositories;

use App\Domains\Sales\Contracts\SaleRepositoryContract;
use App\Domains\Sales\Enums\PaymentStatus;
use App\Domains\Shared\Support\Sort;
use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentSaleRepository implements SaleRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = ['price' => 'price', 'created_at' => 'created_at'];

    public function paginateForBuyer(int $buyerId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Sale::query()
            ->with(['prompt:id,title,slug', 'pack:id,title,slug'])
            ->where('buyer_id', $buyerId)
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForCreator(int $creatorId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Sale::query()
            ->with(['prompt:id,title,slug', 'pack:id,title,slug', 'buyer:id,name'])
            ->where('creator_id', $creatorId)
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Sale::query()
            ->with(['prompt:id,title,slug', 'pack:id,title,slug', 'buyer:id,name', 'creator:id,name'])
            ->when($filters['payment_status'] ?? null, fn ($query, $status) => $query->where('payment_status', $status))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByClientReference(string $reference): ?Sale
    {
        return Sale::query()->where('client_reference', $reference)->first();
    }

    public function hasPurchasedPrompt(int $buyerId, int $promptId): bool
    {
        return Sale::query()
            ->where('buyer_id', $buyerId)
            ->where('prompt_id', $promptId)
            ->where('payment_status', PaymentStatus::Completed)
            ->exists();
    }

    public function hasPurchasedPack(int $buyerId, int $packId): bool
    {
        return Sale::query()
            ->where('buyer_id', $buyerId)
            ->where('pack_id', $packId)
            ->where('payment_status', PaymentStatus::Completed)
            ->exists();
    }

    public function create(array $attributes): Sale
    {
        return Sale::create($attributes);
    }
}
