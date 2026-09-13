<?php

namespace App\Domains\Prompts\Repositories;

use App\Domains\Prompts\Contracts\FolderRepositoryContract;
use App\Models\Folder;
use Illuminate\Support\Collection;

final class EloquentFolderRepository implements FolderRepositoryContract
{
    public function forUser(int $userId): Collection
    {
        return Folder::query()
            ->where('user_id', $userId)
            ->withCount('prompts')
            ->orderBy('name')
            ->get();
    }

    public function findOrFail(int $id): Folder
    {
        return Folder::query()->findOrFail($id);
    }

    public function create(array $attributes): Folder
    {
        return Folder::create($attributes);
    }

    public function update(Folder $folder, array $attributes): Folder
    {
        $folder->update($attributes);

        return $folder->refresh();
    }

    public function delete(Folder $folder): void
    {
        $folder->delete();
    }

    public function countPrompts(Folder $folder): int
    {
        return $folder->prompts()->count();
    }
}
