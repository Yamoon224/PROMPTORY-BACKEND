<?php

namespace App\Domains\Prompts\Http\Controllers;

use App\Domains\Prompts\Http\Requests\RejectPromptRequest;
use App\Domains\Prompts\Http\Resources\PromptResource;
use App\Domains\Prompts\Services\PromptModerationService;
use App\Domains\Shared\Exceptions\OwnershipViolationException;
use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/** File de moderation et decisions d'approbation/rejet, reservees au back-office. */
class PromptModerationController extends Controller
{
    public function __construct(private readonly PromptModerationService $moderation) {}

    public function pending(Request $request): AnonymousResourceCollection
    {
        return PromptResource::collection($this->moderation->pendingQueue(
            $request->only(['sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function submit(Request $request, Prompt $prompt): PromptResource
    {
        if ($prompt->user_id !== $request->user()->id) {
            throw OwnershipViolationException::make();
        }

        return new PromptResource($this->moderation->submit($prompt));
    }

    public function approve(Request $request, Prompt $prompt): PromptResource
    {
        return new PromptResource($this->moderation->approve($prompt, $request->user()->id));
    }

    public function reject(RejectPromptRequest $request, Prompt $prompt): PromptResource
    {
        return new PromptResource($this->moderation->reject($prompt, $request->user()->id, $request->string('reason')->toString()));
    }

    public function archive(Request $request, Prompt $prompt): PromptResource
    {
        if ($prompt->user_id !== $request->user()->id) {
            throw OwnershipViolationException::make();
        }

        return new PromptResource($this->moderation->archive($prompt));
    }

    public function unarchive(Request $request, Prompt $prompt): PromptResource
    {
        if ($prompt->user_id !== $request->user()->id) {
            throw OwnershipViolationException::make();
        }

        return new PromptResource($this->moderation->unarchive($prompt));
    }
}
