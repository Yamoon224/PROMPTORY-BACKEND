<?php

namespace App\Domains\Audit\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ActivityLog */
class ActivityLogResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action->value,
            'user' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name]),
            'prompt' => $this->whenLoaded(
                'prompt',
                fn () => $this->prompt ? ['id' => $this->prompt->id, 'title' => $this->prompt->title, 'slug' => $this->prompt->slug] : null,
            ),
            'created_at' => $this->created_at,
        ];
    }
}
