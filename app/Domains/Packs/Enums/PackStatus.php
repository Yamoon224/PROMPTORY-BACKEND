<?php

namespace App\Domains\Packs\Enums;

enum PackStatus: string
{
    case PendingValidation = 'pending_validation';
    case Published = 'published';
    case Archived = 'archived';
}
