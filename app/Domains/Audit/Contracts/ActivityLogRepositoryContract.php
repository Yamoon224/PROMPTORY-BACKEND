<?php

namespace App\Domains\Audit\Contracts;

use App\Domains\Audit\Enums\ActivityAction;
use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActivityLogRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, ActivityLog>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function record(?int $userId, ?int $promptId, ActivityAction $action): ActivityLog;
}
