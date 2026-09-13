<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 */
class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsToMany<Prompt, $this> */
    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'prompt_tags');
    }
}
