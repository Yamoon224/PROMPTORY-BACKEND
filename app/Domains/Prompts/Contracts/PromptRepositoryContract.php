<?php

namespace App\Domains\Prompts\Contracts;

use App\Models\Prompt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PromptRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Prompt>
     */
    public function paginatePublished(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Prompt>
     */
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Prompt>
     */
    public function paginatePendingValidation(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findOrFail(int $id): Prompt;

    public function findBySlug(string $slug): Prompt;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Prompt;

    /** @param  array<string, mixed>  $attributes */
    public function update(Prompt $prompt, array $attributes): Prompt;

    public function delete(Prompt $prompt): void;

    public function incrementViews(Prompt $prompt): void;

    public function incrementDownloads(Prompt $prompt): void;

    /** @param  list<int>  $tagIds */
    public function syncTags(Prompt $prompt, array $tagIds): void;

    /** @param  list<int>  $categoryIds */
    public function syncCategories(Prompt $prompt, array $categoryIds): void;

    /** @param  list<int>  $iaModelIds */
    public function syncIaModels(Prompt $prompt, array $iaModelIds): void;
}
