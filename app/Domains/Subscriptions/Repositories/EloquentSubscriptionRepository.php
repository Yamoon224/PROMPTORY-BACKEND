<?php

namespace App\Domains\Subscriptions\Repositories;

use App\Domains\Shared\Support\Sort;
use App\Domains\Subscriptions\Contracts\SubscriptionRepositoryContract;
use App\Domains\Subscriptions\Enums\SubscriptionStatus;
use App\Domains\Subscriptions\Enums\SubscriptionType;
use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentSubscriptionRepository implements SubscriptionRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = ['created_at' => 'created_at', 'end_date' => 'end_date'];

    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Subscription::query()
            ->where('user_id', $userId)
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Subscription::query()
            ->with('user:id,name,email')
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findActive(int $userId, SubscriptionType $type): ?Subscription
    {
        return Subscription::query()
            ->where('user_id', $userId)
            ->where('type', $type)
            ->where('status', SubscriptionStatus::Active)
            ->where('end_date', '>=', now()->toDateString())
            ->first();
    }

    public function findOrFail(int $id): Subscription
    {
        return Subscription::query()->findOrFail($id);
    }

    public function create(array $attributes): Subscription
    {
        return Subscription::create($attributes);
    }

    public function update(Subscription $subscription, array $attributes): Subscription
    {
        $subscription->update($attributes);

        return $subscription->refresh();
    }
}
