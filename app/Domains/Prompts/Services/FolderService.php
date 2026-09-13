<?php

namespace App\Domains\Prompts\Services;

use App\Domains\Audit\Enums\ActivityAction;
use App\Domains\Audit\Services\ActivityLogger;
use App\Domains\Prompts\Contracts\FolderRepositoryContract;
use App\Domains\Prompts\Exceptions\FolderNotEmptyException;
use App\Models\Folder;
use Illuminate\Support\Collection;

/** Dossiers personnels, propres a chaque compte. */
final class FolderService
{
    public function __construct(
        private readonly FolderRepositoryContract $folders,
        private readonly ActivityLogger $activity,
    ) {}

    /** @return Collection<int, Folder> */
    public function forUser(int $userId): Collection
    {
        return $this->folders->forUser($userId);
    }

    /** @param  array<string, mixed>  $data */
    public function create(int $userId, array $data): Folder
    {
        $folder = $this->folders->create([...$data, 'user_id' => $userId]);

        $this->activity->record($userId, null, ActivityAction::SaveFolder);

        return $folder;
    }

    /** @param  array<string, mixed>  $data */
    public function update(Folder $folder, array $data): Folder
    {
        return $this->folders->update($folder, $data);
    }

    /** @throws FolderNotEmptyException */
    public function delete(Folder $folder): void
    {
        $prompts = $this->folders->countPrompts($folder);

        if ($prompts > 0) {
            throw FolderNotEmptyException::make($folder->name, $prompts);
        }

        $this->folders->delete($folder);
    }
}
