<?php

namespace App\Domains\Prompts\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

/**
 * Refus de modifier le contenu d'un prompt qui n'est pas en brouillon.
 *
 * Un prompt en attente de moderation ou deja publie a ete lu (ou est en train
 * de l'etre) par un moderateur ou un acheteur : le modifier sous ses pieds
 * romprait la correspondance entre ce qui a ete valide et ce qui est vendu.
 * Le createur doit d'abord l'archiver pour le rouvrir en brouillon.
 */
final class PromptNotEditableException extends DomainException
{
    public static function make(string $status): self
    {
        return new self(
            "Ce prompt ne peut pas etre modifie dans son etat actuel ({$status}). Archivez-le d'abord.",
            'prompt_not_editable',
            409,
            ['status' => $status],
        );
    }
}
