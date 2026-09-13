<?php

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Contracts\CategoryRepositoryContract;
use App\Domains\Shared\Exceptions\ResourceInUseException;
use App\Domains\Shared\Support\Slug;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Referentiel des categories de la marketplace.
 *
 * Partage par tous les createurs : deux prompts en « Ecriture » pointent la
 * meme fiche, sans quoi un filtre par categorie n'en trouverait qu'une partie.
 */
final class CategoryService
{
    public function __construct(private readonly CategoryRepositoryContract $categories) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Category>
     */
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->categories->paginate($filters, $perPage);
    }

    /** @return list<Category> */
    public function all(): array
    {
        return $this->categories->all();
    }

    public function find(int $id): Category
    {
        return $this->categories->findOrFail($id);
    }

    /** @param  array<string, mixed>  $data */
    public function create(array $data): Category
    {
        $data['slug'] ??= Slug::unique(Category::query(), (string) $data['name']);

        return $this->categories->create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function update(Category $category, array $data): Category
    {
        // Le slug ne se regenere pas au renommage : il vit dans des URL deja
        // partagees, et le changer casserait silencieusement chacune d'elles.
        return $this->categories->update($category, $data);
    }

    /** @throws ResourceInUseException */
    public function delete(Category $category): void
    {
        $dependents = $this->categories->countDependents($category);

        if ($dependents > 0) {
            throw ResourceInUseException::make('La categorie', $category->name, $dependents);
        }

        $this->categories->delete($category);
    }
}
