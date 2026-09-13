<?php

namespace App\Domains\Sales\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class PaymentFailedException extends DomainException
{
    public static function make(string $reason): self
    {
        return new self("Le paiement a echoue : {$reason}", 'payment_failed', 402, ['reason' => $reason]);
    }
}
