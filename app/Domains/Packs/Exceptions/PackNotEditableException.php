<?php

namespace App\Domains\Packs\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class PackNotEditableException extends DomainException
{
    public static function make(string $status): self
    {
        return new self(
            "Ce pack ne peut pas etre modifie dans son etat actuel ({$status}). Archivez-le d'abord.",
            'pack_not_editable',
            409,
            ['status' => $status],
        );
    }
}
