<?php

namespace App\Domains\Subscriptions\Contracts;

use App\Domains\Subscriptions\Enums\SubscriptionType;
use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SubscriptionRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Subscription>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Subscription>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findActive(int $userId, SubscriptionType $type): ?Subscription;

    public function findOrFail(int $id): Subscription;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Subscription;

    /** @param  array<string, mixed>  $attributes */
    public function update(Subscription $subscription, array $attributes): Subscription;
}
