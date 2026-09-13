<?php

namespace App\Domains\Packs\Http\Controllers;

use App\Domains\Packs\Http\Requests\RejectPackRequest;
use App\Domains\Packs\Http\Resources\PackResource;
use App\Domains\Packs\Services\PackModerationService;
use App\Domains\Shared\Exceptions\OwnershipViolationException;
use App\Http\Controllers\Controller;
use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PackModerationController extends Controller
{
    public function __construct(private readonly PackModerationService $moderation) {}

    public function pending(Request $request): AnonymousResourceCollection
    {
        return PackResource::collection($this->moderation->pendingQueue(
            $request->only(['sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function approve(Request $request, Pack $pack): PackResource
    {
        return new PackResource($this->moderation->approve($pack, $request->user()->id));
    }

    public function reject(RejectPackRequest $request, Pack $pack): PackResource
    {
        return new PackResource($this->moderation->reject($pack, $request->user()->id, $request->string('reason')->toString()));
    }

    public function archive(Request $request, Pack $pack): PackResource
    {
        if ($pack->user_id !== $request->user()->id) {
            throw OwnershipViolationException::make();
        }

        return new PackResource($this->moderation->archive($pack));
    }

    public function resubmit(Request $request, Pack $pack): PackResource
    {
        if ($pack->user_id !== $request->user()->id) {
            throw OwnershipViolationException::make();
        }

        return new PackResource($this->moderation->resubmit($pack));
    }
}
