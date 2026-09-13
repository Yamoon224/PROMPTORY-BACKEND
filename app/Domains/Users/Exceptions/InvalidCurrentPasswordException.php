<?php

namespace App\Domains\Users\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;

final class InvalidCurrentPasswordException extends DomainException
{
    public static function make(): self
    {
        return new self('Le mot de passe actuel est incorrect.', 'invalid_current_password', 422);
    }
}
