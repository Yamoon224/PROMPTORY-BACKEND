<?php

namespace App\Domains\Sales\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

/** Un prompt ou un pack qui n'est pas publie ne peut pas etre vendu, meme a son createur. */
final class ItemNotPurchasableException extends DomainException
{
    public static function make(string $status): self
    {
        return new self(
            "Cet element n'est pas disponible a la vente (etat actuel : {$status}).",
            'item_not_purchasable',
            409,
            ['status' => $status],
        );
    }
}
