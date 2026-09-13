<?php

namespace App\Models;

use App\Domains\Prompts\Enums\AttachmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $prompt_id
 * @property AttachmentType $type
 * @property string $url
 */
class PromptAttachment extends Model
{
    protected $fillable = ['prompt_id', 'type', 'url'];

    protected function casts(): array
    {
        return ['type' => AttachmentType::class];
    }

    /** @return BelongsTo<Prompt, $this> */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }
}
