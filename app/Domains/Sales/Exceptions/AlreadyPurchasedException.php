<?php

namespace App\Domains\Sales\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class AlreadyPurchasedException extends DomainException
{
    public static function make(): self
    {
        return new self('Vous possedez deja cet element.', 'already_purchased', 409);
    }
}
