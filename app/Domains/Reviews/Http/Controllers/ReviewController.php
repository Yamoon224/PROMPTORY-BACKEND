<?php

namespace App\Domains\Reviews\Http\Controllers;

use App\Domains\Reviews\Http\Requests\StoreReviewRequest;
use App\Domains\Reviews\Http\Resources\ReviewResource;
use App\Domains\Reviews\Services\ReviewService;
use App\Http\Controllers\Controller;
use App\Models\Prompt;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviews) {}

    public function index(Request $request, Prompt $prompt): AnonymousResourceCollection
    {
        return ReviewResource::collection($this->reviews->forPrompt(
            $prompt->id,
            [],
            $request->integer('per_page', 15),
        ));
    }

    public function store(StoreReviewRequest $request, Prompt $prompt): JsonResponse
    {
        $review = $this->reviews->submit(
            $request->user()->id,
            $prompt,
            (int) $request->integer('rating'),
            $request->string('comment')->toString() ?: null,
        );

        return (new ReviewResource($review->load('user:id,name')))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, Review $review): Response
    {
        $this->reviews->delete($review, $request->user()->id);

        return response()->noContent();
    }
}
