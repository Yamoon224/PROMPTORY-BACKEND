<?php

namespace App\Domains\Users\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
