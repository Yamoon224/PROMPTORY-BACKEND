<?php

namespace App\Domains\Sales\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Sale */
class SaleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_reference' => $this->client_reference,
            'prompt' => $this->whenLoaded('prompt', fn () => $this->prompt ? [
                'id' => $this->prompt->id, 'title' => $this->prompt->title, 'slug' => $this->prompt->slug,
            ] : null),
            'pack' => $this->whenLoaded('pack', fn () => $this->pack ? [
                'id' => $this->pack->id, 'title' => $this->pack->title, 'slug' => $this->pack->slug,
            ] : null),
            'buyer' => $this->whenLoaded('buyer', fn () => ['id' => $this->buyer->id, 'name' => $this->buyer->name]),
            'creator' => $this->whenLoaded('creator', fn () => ['id' => $this->creator->id, 'name' => $this->creator->name]),
            'price' => (float) $this->price,
            'commission' => (float) $this->commission,
            'net_amount' => round((float) $this->price - (float) $this->commission, 2),
            'payment_status' => $this->payment_status->value,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
