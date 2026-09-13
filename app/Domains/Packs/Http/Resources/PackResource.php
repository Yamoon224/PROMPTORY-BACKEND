<?php

namespace App\Domains\Packs\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Pack */
class PackResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'status' => $this->status->value,
            'rejection_reason' => $this->rejection_reason,
            'prompts_count' => $this->whenCounted('prompts'),
            'prompts' => $this->whenLoaded('prompts', fn () => $this->prompts->map(fn ($prompt) => [
                'id' => $prompt->id, 'title' => $prompt->title, 'slug' => $prompt->slug, 'price' => (float) $prompt->price,
            ])),
            'creator' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
