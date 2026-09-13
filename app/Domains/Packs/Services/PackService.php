<?php

namespace App\Domains\Packs\Services;

use App\Domains\Packs\Contracts\PackRepositoryContract;
use App\Domains\Packs\Enums\PackStatus;
use App\Domains\Packs\Exceptions\EmptyPackException;
use App\Domains\Packs\Exceptions\PackNotEditableException;
use App\Domains\Shared\Support\Slug;
use App\Models\Pack;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Cycle de vie editorial d'un pack : regroupement de prompts existants du
 * meme createur, vendus a prix unique.
 *
 * Contrairement a un prompt, un pack n'a pas d'etat brouillon (voir le schema
 * du cahier des charges) : il entre directement en attente de validation,
 * la moderation portant sur le prix et la selection plutot que sur un
 * contenu a relire.
 */
final class PackService
{
    public function __construct(private readonly PackRepositoryContract $packs) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Pack>
     */
    public function browsePublished(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->packs->paginatePublished($filters, $perPage);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Pack>
     */
    public function listMine(int $userId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->packs->paginateForUser($userId, $filters, $perPage);
    }

    public function find(int $id): Pack
    {
        return $this->packs->findOrFail($id);
    }

    public function findBySlug(string $slug): Pack
    {
        return $this->packs->findBySlug($slug);
    }

    /**
     * @param  array{title: string, description?: string|null, price: float, prompts: list<int>}  $data
     *
     * @throws EmptyPackException
     */
    public function create(int $userId, array $data): Pack
    {
        if (($data['prompts'] ?? []) === []) {
            throw EmptyPackException::make();
        }

        return DB::transaction(function () use ($userId, $data): Pack {
            $pack = $this->packs->create([
                'user_id' => $userId,
                'title' => $data['title'],
                'slug' => Slug::unique(Pack::query(), $data['title']),
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'status' => PackStatus::PendingValidation,
            ]);

            $this->packs->syncPrompts($pack, $data['prompts']);

            return $pack->fresh('prompts');
        });
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws PackNotEditableException
     */
    public function update(Pack $pack, array $data): Pack
    {
        if (! in_array($pack->status, [PackStatus::PendingValidation, PackStatus::Archived], true)) {
            throw PackNotEditableException::make($pack->status->value);
        }

        return DB::transaction(function () use ($pack, $data): Pack {
            $updated = $this->packs->update($pack, array_intersect_key($data, array_flip(['title', 'description', 'price'])));

            if (array_key_exists('prompts', $data)) {
                if ($data['prompts'] === []) {
                    throw EmptyPackException::make();
                }
                $this->packs->syncPrompts($updated, $data['prompts']);
            }

            return $updated->fresh('prompts');
        });
    }

    public function delete(Pack $pack): void
    {
        $this->packs->delete($pack);
    }
}
