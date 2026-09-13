<?php

namespace App\Models;

use App\Domains\Audit\Enums\ActivityAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Evenement d'usage (analytics produit). Ecriture seule : jamais mis a jour,
 * jamais supprime.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $prompt_id
 * @property ActivityAction $action
 */
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'activity_log';

    protected $fillable = ['user_id', 'prompt_id', 'action'];

    protected function casts(): array
    {
        return ['action' => ActivityAction::class];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Prompt, $this> */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }
}
