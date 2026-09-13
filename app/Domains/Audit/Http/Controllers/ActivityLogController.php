<?php

namespace App\Domains\Audit\Http\Controllers;

use App\Domains\Audit\Http\Resources\ActivityLogResource;
use App\Domains\Audit\Services\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityLogController extends Controller
{
    public function __construct(private readonly ActivityLogger $activity) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $logs = $this->activity->list(
            $request->only(['user_id', 'prompt_id', 'action', 'sort', 'direction']),
            (int) $request->integer('per_page', 15),
        );

        return ActivityLogResource::collection($logs);
    }
}
