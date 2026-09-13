<?php

namespace App\Domains\Audit\Repositories;

use App\Domains\Audit\Contracts\ActivityLogRepositoryContract;
use App\Domains\Audit\Enums\ActivityAction;
use App\Domains\Shared\Support\Sort;
use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentActivityLogRepository implements ActivityLogRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = [
        'created_at' => 'created_at',
        'action' => 'action',
    ];

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return ActivityLog::query()
            ->with(['user:id,name', 'prompt:id,title,slug'])
            ->when($filters['user_id'] ?? null, fn ($query, $id) => $query->where('user_id', $id))
            ->when($filters['prompt_id'] ?? null, fn ($query, $id) => $query->where('prompt_id', $id))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function record(?int $userId, ?int $promptId, ActivityAction $action): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId,
            'prompt_id' => $promptId,
            'action' => $action,
        ]);
    }
}
