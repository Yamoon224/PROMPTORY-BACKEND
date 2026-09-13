<?php

namespace App\Domains\Payments\Enums;

enum GatewayStatus: string
{
    case Succeeded = 'succeeded';
    case Failed = 'failed';
    case Pending = 'pending';
}
