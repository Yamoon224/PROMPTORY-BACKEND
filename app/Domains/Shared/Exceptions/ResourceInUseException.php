<?php

namespace App\Domains\Shared\Exceptions;

/**
 * Refus de supprimer une fiche du referentiel encore citee ailleurs
 * (categorie, tag ou outil IA rattache a des prompts existants).
 *
 * Une classe unique plutot qu'une par entite : le refus est le meme, seul le
 * nom change.
 */
final class ResourceInUseException extends DomainException
{
    public static function make(string $resource, string $label, int $dependents): self
    {
        return new self(
            "{$resource} « {$label} » est utilise par {$dependents} prompt(s) et ne peut pas etre supprime.",
            'resource_in_use',
            409,
            ['resource' => $resource, 'label' => $label, 'dependents' => $dependents],
        );
    }
}
