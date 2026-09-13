<?php

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Contracts\TagRepositoryContract;
use App\Domains\Shared\Exceptions\ResourceInUseException;
use App\Domains\Shared\Support\Slug;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class TagService
{
    public function __construct(private readonly TagRepositoryContract $tags) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Tag>
     */
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->tags->paginate($filters, $perPage);
    }

    /** @return list<Tag> */
    public function all(): array
    {
        return $this->tags->all();
    }

    public function find(int $id): Tag
    {
        return $this->tags->findOrFail($id);
    }

    /** @param  array<string, mixed>  $data */
    public function create(array $data): Tag
    {
        $data['slug'] ??= Slug::unique(Tag::query(), (string) $data['name']);

        return $this->tags->create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function update(Tag $tag, array $data): Tag
    {
        return $this->tags->update($tag, $data);
    }

    /** @throws ResourceInUseException */
    public function delete(Tag $tag): void
    {
        $dependents = $this->tags->countDependents($tag);

        if ($dependents > 0) {
            throw ResourceInUseException::make('Le tag', $tag->name, $dependents);
        }

        $this->tags->delete($tag);
    }
}
