<?php

namespace App\Domains\Prompts\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class PromptNotSubmittableException extends DomainException
{
    public static function make(string $status): self
    {
        return new self(
            "Seul un prompt en brouillon peut etre soumis a validation (etat actuel : {$status}).",
            'prompt_not_submittable',
            409,
            ['status' => $status],
        );
    }
}
