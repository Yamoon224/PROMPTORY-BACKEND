<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Outil IA cible par un prompt (ChatGPT, Midjourney…). Referentiel partage,
 * gere par la moderation.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $is_active
 */
class IaModel extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsToMany<Prompt, $this> */
    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'prompt_ia_models');
    }
}
