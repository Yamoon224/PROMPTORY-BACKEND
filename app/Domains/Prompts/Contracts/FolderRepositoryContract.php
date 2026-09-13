<?php

namespace App\Domains\Prompts\Contracts;

use App\Models\Folder;
use Illuminate\Support\Collection;

interface FolderRepositoryContract
{
    /** @return Collection<int, Folder> */
    public function forUser(int $userId): Collection;

    public function findOrFail(int $id): Folder;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Folder;

    /** @param  array<string, mixed>  $attributes */
    public function update(Folder $folder, array $attributes): Folder;

    public function delete(Folder $folder): void;

    public function countPrompts(Folder $folder): int;
}
