<?php

namespace App\Domains\Prompts\Repositories;

use App\Domains\Prompts\Contracts\PromptRepositoryContract;
use App\Domains\Prompts\Enums\PromptStatus;
use App\Domains\Shared\Support\Sort;
use App\Models\Prompt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class EloquentPromptRepository implements PromptRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = [
        'title' => 'title',
        'price' => 'price',
        'created_at' => 'created_at',
        'views' => 'views_count',
        'downloads' => 'downloads_count',
    ];

    public function paginatePublished(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->where('status', PromptStatus::Published)
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->where('user_id', $userId)
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['folder_id'] ?? null, fn ($query, $id) => $query->where('folder_id', $id))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'desc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginatePendingValidation(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->baseQuery($filters)
            ->where('status', PromptStatus::PendingValidation)
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'created_at', 'asc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findOrFail(int $id): Prompt
    {
        return Prompt::query()
            ->with(['user:id,name', 'tags', 'categories', 'iaModels', 'attachments'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->findOrFail($id);
    }

    public function findBySlug(string $slug): Prompt
    {
        return Prompt::query()
            ->with(['user:id,name', 'tags', 'categories', 'iaModels', 'attachments', 'reviews.user:id,name'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function create(array $attributes): Prompt
    {
        return Prompt::create($attributes);
    }

    public function update(Prompt $prompt, array $attributes): Prompt
    {
        $prompt->update($attributes);

        return $prompt->refresh();
    }

    public function delete(Prompt $prompt): void
    {
        $prompt->delete();
    }

    public function incrementViews(Prompt $prompt): void
    {
        $prompt->increment('views_count');
    }

    public function incrementDownloads(Prompt $prompt): void
    {
        $prompt->increment('downloads_count');
    }

    public function syncTags(Prompt $prompt, array $tagIds): void
    {
        $prompt->tags()->sync($tagIds);
    }

    public function syncCategories(Prompt $prompt, array $categoryIds): void
    {
        $prompt->categories()->sync($categoryIds);
    }

    public function syncIaModels(Prompt $prompt, array $iaModelIds): void
    {
        $prompt->iaModels()->sync($iaModelIds);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Prompt>
     */
    private function baseQuery(array $filters): Builder
    {
        return Prompt::query()
            ->with(['user:id,name', 'tags:id,name,slug', 'categories:id,name,slug'])
            ->withCount(['reviews'])
            ->withAvg('reviews', 'rating')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($filters['category'] ?? null, fn ($query, $slug) => $query->whereHas(
                'categories',
                fn ($categories) => $categories->where('slug', $slug),
            ))
            ->when($filters['tag'] ?? null, fn ($query, $slug) => $query->whereHas(
                'tags',
                fn ($tags) => $tags->where('slug', $slug),
            ))
            ->when($filters['ia_model'] ?? null, fn ($query, $slug) => $query->whereHas(
                'iaModels',
                fn ($models) => $models->where('slug', $slug),
            ))
            ->when($filters['free'] ?? null, fn ($query) => $query->where('price', 0))
            ->when($filters['min_price'] ?? null, fn ($query, $price) => $query->where('price', '>=', $price))
            ->when($filters['max_price'] ?? null, fn ($query, $price) => $query->where('price', '<=', $price));
    }
}
