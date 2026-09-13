<?php

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Contracts\IaModelRepositoryContract;
use App\Domains\Shared\Exceptions\ResourceInUseException;
use App\Domains\Shared\Support\Slug;
use App\Models\IaModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class IaModelService
{
    public function __construct(private readonly IaModelRepositoryContract $iaModels) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, IaModel>
     */
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->iaModels->paginate($filters, $perPage);
    }

    /** @return list<IaModel> */
    public function allActive(): array
    {
        return $this->iaModels->allActive();
    }

    public function find(int $id): IaModel
    {
        return $this->iaModels->findOrFail($id);
    }

    /** @param  array<string, mixed>  $data */
    public function create(array $data): IaModel
    {
        $data['slug'] ??= Slug::unique(IaModel::query(), (string) $data['name']);
        $data['is_active'] ??= true;

        return $this->iaModels->create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function update(IaModel $iaModel, array $data): IaModel
    {
        return $this->iaModels->update($iaModel, $data);
    }

    /** @throws ResourceInUseException */
    public function delete(IaModel $iaModel): void
    {
        $dependents = $this->iaModels->countDependents($iaModel);

        if ($dependents > 0) {
            throw ResourceInUseException::make("L'outil IA", $iaModel->name, $dependents);
        }

        $this->iaModels->delete($iaModel);
    }
}
