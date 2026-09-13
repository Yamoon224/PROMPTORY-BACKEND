<?php

namespace App\Domains\Packs\Http\Controllers;

use App\Domains\Packs\Http\Requests\StorePackRequest;
use App\Domains\Packs\Http\Requests\UpdatePackRequest;
use App\Domains\Packs\Http\Resources\PackResource;
use App\Domains\Packs\Services\PackService;
use App\Domains\Shared\Exceptions\OwnershipViolationException;
use App\Http\Controllers\Controller;
use App\Models\Pack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PackController extends Controller
{
    public function __construct(private readonly PackService $packs) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return PackResource::collection($this->packs->browsePublished(
            $request->only(['search', 'sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        return PackResource::collection($this->packs->listMine(
            $request->user()->id,
            $request->only(['status', 'sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function store(StorePackRequest $request): JsonResponse
    {
        return (new PackResource($this->packs->create($request->user()->id, $request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $slug): PackResource
    {
        return new PackResource($this->packs->findBySlug($slug));
    }

    public function update(UpdatePackRequest $request, Pack $pack): PackResource
    {
        $this->authorizeOwner($request, $pack);

        return new PackResource($this->packs->update($pack, $request->validated()));
    }

    public function destroy(Request $request, Pack $pack): Response
    {
        $this->authorizeOwner($request, $pack);
        $this->packs->delete($pack);

        return response()->noContent();
    }

    private function authorizeOwner(Request $request, Pack $pack): void
    {
        if ($pack->user_id !== $request->user()->id) {
            throw OwnershipViolationException::make();
        }
    }
}
