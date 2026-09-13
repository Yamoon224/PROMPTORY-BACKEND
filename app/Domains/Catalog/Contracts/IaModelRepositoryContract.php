<?php

namespace App\Domains\Catalog\Contracts;

use App\Models\IaModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IaModelRepositoryContract
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, IaModel>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /** @return list<IaModel> */
    public function allActive(): array;

    public function findOrFail(int $id): IaModel;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): IaModel;

    /** @param  array<string, mixed>  $attributes */
    public function update(IaModel $iaModel, array $attributes): IaModel;

    public function delete(IaModel $iaModel): void;

    public function countDependents(IaModel $iaModel): int;
}
