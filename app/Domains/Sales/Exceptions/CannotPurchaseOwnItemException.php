<?php

namespace App\Domains\Sales\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class CannotPurchaseOwnItemException extends DomainException
{
    public static function make(): self
    {
        return new self("Vous ne pouvez pas acheter votre propre creation.", 'cannot_purchase_own_item', 422);
    }
}
