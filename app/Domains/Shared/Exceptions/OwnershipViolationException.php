<?php

namespace App\Domains\Shared\Exceptions;

/**
 * Refus d'agir sur une ressource personnelle (dossier, prompt, pack) qui
 * n'appartient pas au compte courant.
 *
 * Une classe unique plutot qu'une verification ad hoc par controleur : le
 * refus et son code applicatif sont toujours les memes, et un `404` aurait
 * masque au proprietaire legitime la difference entre « n'existe pas » et
 * « n'est pas a vous » — utile pour un support qui doit diagnostiquer un
 * lien partage par erreur.
 */
final class OwnershipViolationException extends DomainException
{
    public static function make(): self
    {
        return new self(
            "Cette ressource n'appartient pas a votre compte.",
            'ownership_violation',
            403,
        );
    }
}
