<?php

namespace App\Domains\Subscriptions\Enums;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case Canceled = 'canceled';
    case Expired = 'expired';
}
