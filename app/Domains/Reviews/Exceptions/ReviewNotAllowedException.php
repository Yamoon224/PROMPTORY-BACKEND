<?php

namespace App\Domains\Reviews\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

/** Refus de noter un prompt qui n'a pas ete achete (ou publie par soi-meme). */
final class ReviewNotAllowedException extends DomainException
{
    public static function notPurchased(): self
    {
        return new self(
            "Vous devez avoir achete ce prompt pour le noter.",
            'review_not_allowed',
            403,
        );
    }

    public static function ownPrompt(): self
    {
        return new self(
            "Vous ne pouvez pas noter votre propre prompt.",
            'review_not_allowed',
            403,
        );
    }
}
