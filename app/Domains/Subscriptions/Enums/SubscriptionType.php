<?php

namespace App\Domains\Subscriptions\Enums;

enum SubscriptionType: string
{
    case CreatorPremium = 'creator_premium';
    case ExtensionPremium = 'extension_premium';
}
