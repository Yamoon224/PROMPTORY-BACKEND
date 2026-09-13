<?php

namespace App\Domains\Payments\Enums;

/**
 * Moyens de paiement proposes a l'acheteur, conformement au cahier des
 * charges. Chaque valeur correspond a une cle de `config('promptory.gateways')`
 * et donc a une classe `PaymentGatewayContract` concrete.
 */
enum PaymentMethod: string
{
    case Stripe = 'stripe';
    case Paypal = 'paypal';
}
