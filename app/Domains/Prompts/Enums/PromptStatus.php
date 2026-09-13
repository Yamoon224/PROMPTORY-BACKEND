<?php

namespace App\Domains\Prompts\Enums;

/**
 * Cycle de vie d'un prompt.
 *
 *   draft -> pending_validation -> published
 *                               -> draft (rejete, a corriger)
 *   published <-> archived
 *
 * Seul un `published` apparait sur la marketplace ou dans une recherche
 * publique : les autres etats ne regardent que leur createur et la
 * moderation.
 */
enum PromptStatus: string
{
    case Draft = 'draft';
    case PendingValidation = 'pending_validation';
    case Published = 'published';
    case Archived = 'archived';
}
