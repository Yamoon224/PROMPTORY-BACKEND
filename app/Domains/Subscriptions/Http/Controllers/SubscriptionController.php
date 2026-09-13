<?php

namespace App\Domains\Subscriptions\Http\Controllers;

use App\Domains\Payments\Enums\PaymentMethod;
use App\Domains\Subscriptions\Enums\SubscriptionType;
use App\Domains\Subscriptions\Http\Requests\SubscribeRequest;
use App\Domains\Subscriptions\Http\Resources\SubscriptionResource;
use App\Domains\Subscriptions\Services\SubscriptionService;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptions) {}

    public function mine(Request $request): AnonymousResourceCollection
    {
        return SubscriptionResource::collection($this->subscriptions->listForUser(
            $request->user()->id,
            $request->only(['sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return SubscriptionResource::collection($this->subscriptions->list(
            $request->only(['type', 'status', 'sort', 'direction']),
            $request->integer('per_page', 15),
        ));
    }

    public function store(SubscribeRequest $request): JsonResponse
    {
        $subscription = $this->subscriptions->subscribe(
            $request->user(),
            SubscriptionType::from($request->string('type')->toString()),
            PaymentMethod::from($request->string('payment_method')->toString()),
            $request->string('payment_token')->toString() ?: null,
        );

        return (new SubscriptionResource($subscription))->response()->setStatusCode(201);
    }

    public function cancel(Request $request, Subscription $subscription): SubscriptionResource
    {
        return new SubscriptionResource($this->subscriptions->cancel($subscription, $request->user()->id));
    }
}
