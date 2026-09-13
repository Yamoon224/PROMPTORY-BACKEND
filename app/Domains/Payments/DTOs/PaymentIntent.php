<?php

namespace App\Domains\Payments\DTOs;

/** Ce que le domaine demande a l'agregateur : jamais de moyen de paiement en clair. */
final readonly class PaymentIntent
{
    public function __construct(
        public string $reference,
        public float $amount,
        public string $currency,
        public string $description,
        /** Jeton de moyen de paiement fourni par le SDK cote client (Stripe.js…), jamais un numero de carte. */
        public ?string $paymentToken = null,
    ) {}
}
