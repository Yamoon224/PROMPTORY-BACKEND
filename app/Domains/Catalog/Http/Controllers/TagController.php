<?php

namespace App\Domains\Catalog\Http\Controllers;

use App\Domains\Catalog\Http\Requests\StoreTagRequest;
use App\Domains\Catalog\Http\Requests\UpdateTagRequest;
use App\Domains\Catalog\Http\Resources\TagResource;
use App\Domains\Catalog\Services\TagService;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function __construct(private readonly TagService $tags) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->boolean('all')) {
            return TagResource::collection($this->tags->all());
        }

        return TagResource::collection($this->tags->list(
            $request->only(['search', 'sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        return (new TagResource($this->tags->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Tag $tag): TagResource
    {
        return new TagResource($this->tags->find($tag->id)->loadCount('prompts'));
    }

    public function update(UpdateTagRequest $request, Tag $tag): TagResource
    {
        return new TagResource($this->tags->update($tag, $request->validated()));
    }

    public function destroy(Tag $tag): Response
    {
        $this->tags->delete($tag);

        return response()->noContent();
    }
}
