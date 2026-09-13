<?php

namespace App\Domains\Catalog\Repositories;

use App\Domains\Catalog\Contracts\IaModelRepositoryContract;
use App\Domains\Shared\Support\Sort;
use App\Models\IaModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentIaModelRepository implements IaModelRepositoryContract
{
    /** @var array<string, string> */
    private const SORTABLE = ['name' => 'name', 'created_at' => 'created_at'];

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return IaModel::query()
            ->withCount('prompts')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->tap(fn ($query) => Sort::apply($query, $filters, self::SORTABLE, 'name', 'asc'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function allActive(): array
    {
        return IaModel::query()->where('is_active', true)->orderBy('name')->get()->all();
    }

    public function findOrFail(int $id): IaModel
    {
        return IaModel::query()->findOrFail($id);
    }

    public function create(array $attributes): IaModel
    {
        return IaModel::create($attributes);
    }

    public function update(IaModel $iaModel, array $attributes): IaModel
    {
        $iaModel->update($attributes);

        return $iaModel->refresh();
    }

    public function delete(IaModel $iaModel): void
    {
        $iaModel->delete();
    }

    public function countDependents(IaModel $iaModel): int
    {
        return $iaModel->prompts()->count();
    }
}
