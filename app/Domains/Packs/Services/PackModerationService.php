<?php

namespace App\Domains\Packs\Services;

use App\Domains\Packs\Contracts\PackRepositoryContract;
use App\Domains\Packs\Enums\PackStatus;
use App\Domains\Packs\Exceptions\PackNotModeratableException;
use App\Models\Pack;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

final class PackModerationService
{
    public function __construct(private readonly PackRepositoryContract $packs) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Pack>
     */
    public function pendingQueue(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->packs->paginatePendingValidation($filters, $perPage);
    }

    /** @throws PackNotModeratableException */
    public function approve(Pack $pack, int $moderatorId): Pack
    {
        $this->guardPending($pack);

        return $this->packs->update($pack, [
            'status' => PackStatus::Published,
            'reviewed_by' => $moderatorId,
            'reviewed_at' => Carbon::now(),
            'rejection_reason' => null,
        ]);
    }

    /** @throws PackNotModeratableException */
    public function reject(Pack $pack, int $moderatorId, string $reason): Pack
    {
        $this->guardPending($pack);

        return $this->packs->update($pack, [
            'status' => PackStatus::Archived,
            'reviewed_by' => $moderatorId,
            'reviewed_at' => Carbon::now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function archive(Pack $pack): Pack
    {
        return $this->packs->update($pack, ['status' => PackStatus::Archived]);
    }

    /** Repasse un pack archive en validation : sa selection ou son prix a pu changer entre-temps. */
    public function resubmit(Pack $pack): Pack
    {
        return $this->packs->update($pack, ['status' => PackStatus::PendingValidation, 'rejection_reason' => null]);
    }

    /** @throws PackNotModeratableException */
    private function guardPending(Pack $pack): void
    {
        if ($pack->status !== PackStatus::PendingValidation) {
            throw PackNotModeratableException::make($pack->status->value);
        }
    }
}
