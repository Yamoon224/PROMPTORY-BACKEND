<?php

namespace App\Domains\Prompts\Http\Controllers;

use App\Domains\Prompts\Http\Requests\StoreFolderRequest;
use App\Domains\Prompts\Http\Requests\UpdateFolderRequest;
use App\Domains\Prompts\Http\Resources\FolderResource;
use App\Domains\Prompts\Services\FolderService;
use App\Domains\Shared\Exceptions\OwnershipViolationException;
use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FolderController extends Controller
{
    public function __construct(private readonly FolderService $folders) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return FolderResource::collection($this->folders->forUser($request->user()->id));
    }

    public function store(StoreFolderRequest $request): JsonResponse
    {
        return (new FolderResource($this->folders->create($request->user()->id, $request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateFolderRequest $request, Folder $folder): FolderResource
    {
        $this->authorizeOwner($request, $folder);

        return new FolderResource($this->folders->update($folder, $request->validated()));
    }

    public function destroy(Request $request, Folder $folder): Response
    {
        $this->authorizeOwner($request, $folder);
        $this->folders->delete($folder);

        return response()->noContent();
    }

    private function authorizeOwner(Request $request, Folder $folder): void
    {
        if ($folder->user_id !== $request->user()->id) {
            throw OwnershipViolationException::make();
        }
    }
}
