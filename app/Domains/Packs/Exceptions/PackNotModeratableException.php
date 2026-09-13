<?php

namespace App\Domains\Packs\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class PackNotModeratableException extends DomainException
{
    public static function make(string $status): self
    {
        return new self(
            "Seul un pack en attente de validation peut etre approuve ou rejete (etat actuel : {$status}).",
            'pack_not_moderatable',
            409,
            ['status' => $status],
        );
    }
}
