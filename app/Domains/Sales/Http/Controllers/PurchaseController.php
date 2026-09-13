<?php

namespace App\Domains\Sales\Http\Controllers;

use App\Domains\Sales\Http\Requests\PurchasePromptRequest;
use App\Domains\Sales\Http\Resources\SaleResource;
use App\Domains\Sales\Services\PurchaseService;
use App\Http\Controllers\Controller;
use App\Models\Pack;
use App\Models\Prompt;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchases) {}

    public function purchasePrompt(PurchasePromptRequest $request, Prompt $prompt): JsonResponse
    {
        $sale = $this->purchases->purchasePrompt(
            $request->user(),
            $prompt,
            $request->string('payment_token')->toString() ?: null,
            $request->string('client_reference')->toString() ?: null,
        );

        return (new SaleResource($sale->load(['prompt:id,title,slug'])))->response()->setStatusCode(201);
    }

    public function purchasePack(PurchasePromptRequest $request, Pack $pack): JsonResponse
    {
        $sale = $this->purchases->purchasePack(
            $request->user(),
            $pack,
            $request->string('payment_token')->toString() ?: null,
            $request->string('client_reference')->toString() ?: null,
        );

        return (new SaleResource($sale->load(['pack:id,title,slug'])))->response()->setStatusCode(201);
    }
}
