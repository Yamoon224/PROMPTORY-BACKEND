<?php

namespace App\Models;

use App\Domains\Packs\Enums\PackStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Lot de prompts groupes par un createur, vendu a prix unique.
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property float $price
 * @property PackStatus $status
 * @property Carbon|null $reviewed_at
 */
class Pack extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'price', 'status',
        'reviewed_by', 'reviewed_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => PackStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsToMany<Prompt, $this> */
    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'pack_prompts');
    }

    /** @return HasMany<Sale, $this> */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
