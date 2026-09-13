<?php

namespace App\Domains\Subscriptions\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class AlreadySubscribedException extends DomainException
{
    public static function make(string $type): self
    {
        return new self("Vous avez deja un abonnement {$type} actif.", 'already_subscribed', 409, ['type' => $type]);
    }
}
