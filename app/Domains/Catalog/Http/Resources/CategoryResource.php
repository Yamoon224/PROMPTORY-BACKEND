<?php

namespace App\Domains\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Category */
class CategoryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent' => $this->whenLoaded('parent', fn () => $this->parent ? [
                'id' => $this->parent->id, 'name' => $this->parent->name, 'slug' => $this->parent->slug,
            ] : null),
            'prompts_count' => $this->whenCounted('prompts'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
