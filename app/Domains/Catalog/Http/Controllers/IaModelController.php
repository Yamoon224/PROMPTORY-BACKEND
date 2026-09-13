<?php

namespace App\Domains\Catalog\Http\Controllers;

use App\Domains\Catalog\Http\Requests\StoreIaModelRequest;
use App\Domains\Catalog\Http\Requests\UpdateIaModelRequest;
use App\Domains\Catalog\Http\Resources\IaModelResource;
use App\Domains\Catalog\Services\IaModelService;
use App\Http\Controllers\Controller;
use App\Models\IaModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class IaModelController extends Controller
{
    public function __construct(private readonly IaModelService $iaModels) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->boolean('all')) {
            return IaModelResource::collection($this->iaModels->allActive());
        }

        return IaModelResource::collection($this->iaModels->list(
            $request->only(['search', 'sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function store(StoreIaModelRequest $request): JsonResponse
    {
        return (new IaModelResource($this->iaModels->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(IaModel $iaModel): IaModelResource
    {
        return new IaModelResource($this->iaModels->find($iaModel->id)->loadCount('prompts'));
    }

    public function update(UpdateIaModelRequest $request, IaModel $iaModel): IaModelResource
    {
        return new IaModelResource($this->iaModels->update($iaModel, $request->validated()));
    }

    public function destroy(IaModel $iaModel): Response
    {
        $this->iaModels->delete($iaModel);

        return response()->noContent();
    }
}
