<?php

namespace App\Domains\Prompts\Services;

use App\Domains\Audit\Enums\ActivityAction;
use App\Domains\Audit\Services\ActivityLogger;
use App\Domains\Prompts\Contracts\PromptRepositoryContract;
use App\Domains\Prompts\Enums\PromptStatus;
use App\Domains\Prompts\Exceptions\PromptNotModeratableException;
use App\Domains\Prompts\Exceptions\PromptNotSubmittableException;
use App\Models\Prompt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Workflow de moderation, au sens du cahier des charges :
 *
 *   draft -> pending_validation -> published
 *                               -> draft (rejete)
 *   published <-> archived
 *
 * Isole de `PromptService` (edition de contenu) : la moderation change un
 * statut et trace une decision humaine, elle ne touche jamais au contenu.
 */
final class PromptModerationService
{
    public function __construct(
        private readonly PromptRepositoryContract $prompts,
        private readonly ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Prompt>
     */
    public function pendingQueue(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->prompts->paginatePendingValidation($filters, $perPage);
    }

    /** @throws PromptNotSubmittableException */
    public function submit(Prompt $prompt): Prompt
    {
        if ($prompt->status !== PromptStatus::Draft) {
            throw PromptNotSubmittableException::make($prompt->status->value);
        }

        $updated = $this->prompts->update($prompt, [
            'status' => PromptStatus::PendingValidation,
            'rejection_reason' => null,
        ]);

        $this->activity->record($prompt->user_id, $prompt->id, ActivityAction::SubmitValidation);

        return $updated;
    }

    /** @throws PromptNotModeratableException */
    public function approve(Prompt $prompt, int $moderatorId): Prompt
    {
        $this->guardPending($prompt);

        $updated = $this->prompts->update($prompt, [
            'status' => PromptStatus::Published,
            'reviewed_by' => $moderatorId,
            'reviewed_at' => Carbon::now(),
            'rejection_reason' => null,
        ]);

        $this->activity->record($moderatorId, $prompt->id, ActivityAction::Approve);

        return $updated;
    }

    /** @throws PromptNotModeratableException */
    public function reject(Prompt $prompt, int $moderatorId, string $reason): Prompt
    {
        $this->guardPending($prompt);

        $updated = $this->prompts->update($prompt, [
            'status' => PromptStatus::Draft,
            'reviewed_by' => $moderatorId,
            'reviewed_at' => Carbon::now(),
            'rejection_reason' => $reason,
        ]);

        $this->activity->record($moderatorId, $prompt->id, ActivityAction::Reject);

        return $updated;
    }

    /** Retire un prompt publie de la marketplace, sans le supprimer ni perdre son historique de ventes. */
    public function archive(Prompt $prompt): Prompt
    {
        return $this->prompts->update($prompt, ['status' => PromptStatus::Archived]);
    }

    /** Republie un prompt archive : son contenu a deja ete valide, il n'a pas besoin d'un second passage en moderation. */
    public function unarchive(Prompt $prompt): Prompt
    {
        return $this->prompts->update($prompt, ['status' => PromptStatus::Published]);
    }

    /** @throws PromptNotModeratableException */
    private function guardPending(Prompt $prompt): void
    {
        if ($prompt->status !== PromptStatus::PendingValidation) {
            throw PromptNotModeratableException::make($prompt->status->value);
        }
    }
}
