<?php

namespace App\Domains\Prompts\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class FolderNotEmptyException extends DomainException
{
    public static function make(string $name, int $prompts): self
    {
        return new self(
            "Le dossier « {$name} » contient encore {$prompts} prompt(s). Deplacez-les avant de le supprimer.",
            'folder_not_empty',
            409,
            ['name' => $name, 'prompts' => $prompts],
        );
    }
}
