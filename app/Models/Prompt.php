<?php

namespace App\Models;

use App\Domains\Prompts\Enums\PromptStatus;
use Database\Factories\PromptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Prompt mis en vente ou partage gratuitement par un createur.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $folder_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property float $price
 * @property PromptStatus $status
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property string|null $rejection_reason
 * @property int $views_count
 * @property int $downloads_count
 */
class Prompt extends Model
{
    /** @use HasFactory<PromptFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'folder_id', 'title', 'slug', 'content', 'price', 'status',
        'reviewed_by', 'reviewed_at', 'rejection_reason', 'views_count', 'downloads_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => PromptStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Le createur du prompt.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, $this> */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** @return BelongsTo<Folder, $this> */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /** @return HasMany<PromptAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(PromptAttachment::class);
    }

    /** @return BelongsToMany<Tag, $this> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'prompt_tags');
    }

    /** @return BelongsToMany<Category, $this> */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'prompt_categories');
    }

    /** @return BelongsToMany<IaModel, $this> */
    public function iaModels(): BelongsToMany
    {
        return $this->belongsToMany(IaModel::class, 'prompt_ia_models');
    }

    /** @return BelongsToMany<Pack, $this> */
    public function packs(): BelongsToMany
    {
        return $this->belongsToMany(Pack::class, 'pack_prompts');
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return HasMany<Sale, $this> */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
