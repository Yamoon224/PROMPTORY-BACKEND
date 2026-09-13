<?php

namespace App\Domains\Packs\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class EmptyPackException extends DomainException
{
    public static function make(): self
    {
        return new self('Un pack doit contenir au moins un prompt.', 'empty_pack', 422);
    }
}
