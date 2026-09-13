<?php

namespace App\Domains\Prompts\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class PromptNotModeratableException extends DomainException
{
    public static function make(string $status): self
    {
        return new self(
            "Seul un prompt en attente de validation peut etre approuve ou rejete (etat actuel : {$status}).",
            'prompt_not_moderatable',
            409,
            ['status' => $status],
        );
    }
}
