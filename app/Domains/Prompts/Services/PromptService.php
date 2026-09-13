<?php

namespace App\Domains\Prompts\Services;

use App\Domains\Audit\Enums\ActivityAction;
use App\Domains\Audit\Services\ActivityLogger;
use App\Domains\Prompts\Contracts\PromptRepositoryContract;
use App\Domains\Prompts\Enums\PromptStatus;
use App\Domains\Prompts\Exceptions\PromptNotEditableException;
use App\Domains\Shared\Support\Slug;
use App\Models\Prompt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Cycle de vie editorial d'un prompt : creation, edition, rangement, suppression.
 *
 * La moderation (soumission, approbation, rejet, archivage) vit dans
 * `PromptModerationService` : deux responsabilites, deux classes, conformement
 * au principe de responsabilite unique.
 */
final class PromptService
{
    public function __construct(
        private readonly PromptRepositoryContract $prompts,
        private readonly ActivityLogger $activity,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Prompt>
     */
    public function browsePublished(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->prompts->paginatePublished($filters, $perPage);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Prompt>
     */
    public function listMine(int $userId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->prompts->paginateForUser($userId, $filters, $perPage);
    }

    public function find(int $id): Prompt
    {
        return $this->prompts->findOrFail($id);
    }

    /** Consultation publique : incremente le compteur de vues et trace l'evenement. */
    public function findBySlugForViewing(string $slug, ?int $viewerId): Prompt
    {
        $prompt = $this->prompts->findBySlug($slug);

        if ($prompt->status === PromptStatus::Published) {
            $this->prompts->incrementViews($prompt);
            $this->activity->record($viewerId, $prompt->id, ActivityAction::View);
        }

        return $prompt;
    }

    /**
     * @param  array{title: string, content: string, price?: float, folder_id?: int|null, tags?: list<int>, categories?: list<int>, ia_models?: list<int>}  $data
     */
    public function create(int $userId, array $data): Prompt
    {
        return DB::transaction(function () use ($userId, $data): Prompt {
            $prompt = $this->prompts->create([
                'user_id' => $userId,
                'folder_id' => $data['folder_id'] ?? null,
                'title' => $data['title'],
                'slug' => Slug::unique(Prompt::query(), $data['title']),
                'content' => $data['content'],
                'price' => $data['price'] ?? 0,
                'status' => PromptStatus::Draft,
            ]);

            $this->syncRelations($prompt, $data);

            return $prompt->fresh(['tags', 'categories', 'iaModels']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws PromptNotEditableException
     */
    public function update(Prompt $prompt, array $data): Prompt
    {
        if ($prompt->status !== PromptStatus::Draft) {
            throw PromptNotEditableException::make($prompt->status->value);
        }

        return DB::transaction(function () use ($prompt, $data): Prompt {
            $updated = $this->prompts->update($prompt, array_intersect_key($data, array_flip([
                'title', 'content', 'price', 'folder_id',
            ])));

            $this->syncRelations($updated, $data);

            $this->activity->record($prompt->user_id, $prompt->id, ActivityAction::Edit);

            return $updated->fresh(['tags', 'categories', 'iaModels']);
        });
    }

    public function delete(Prompt $prompt): void
    {
        $this->prompts->delete($prompt);
    }

    /** Achat ou telechargement d'un prompt gratuit : incremente le compteur et trace l'evenement. */
    public function recordDownload(Prompt $prompt, ?int $userId): void
    {
        $this->prompts->incrementDownloads($prompt);
        $this->activity->record($userId, $prompt->id, ActivityAction::Download);
    }

    /** @param  array<string, mixed>  $data */
    private function syncRelations(Prompt $prompt, array $data): void
    {
        if (array_key_exists('tags', $data)) {
            $this->prompts->syncTags($prompt, $data['tags'] ?? []);
        }

        if (array_key_exists('categories', $data)) {
            $this->prompts->syncCategories($prompt, $data['categories'] ?? []);
        }

        if (array_key_exists('ia_models', $data)) {
            $this->prompts->syncIaModels($prompt, $data['ia_models'] ?? []);
        }
    }
}
