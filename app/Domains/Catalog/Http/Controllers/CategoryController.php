<?php

namespace App\Domains\Catalog\Http\Controllers;

use App\Domains\Catalog\Http\Requests\StoreCategoryRequest;
use App\Domains\Catalog\Http\Requests\UpdateCategoryRequest;
use App\Domains\Catalog\Http\Resources\CategoryResource;
use App\Domains\Catalog\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categories) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->boolean('all')) {
            return CategoryResource::collection($this->categories->all());
        }

        return CategoryResource::collection($this->categories->list(
            $request->only(['search', 'parent_id', 'sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        return (new CategoryResource($this->categories->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($this->categories->find($category->id)->loadCount('prompts'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        return new CategoryResource($this->categories->update($category, $request->validated()));
    }

    public function destroy(Category $category): Response
    {
        $this->categories->delete($category);

        return response()->noContent();
    }
}
