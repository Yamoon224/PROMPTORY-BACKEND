<?php

namespace App\Domains\Payments\Contracts;

use App\Domains\Payments\DTOs\PaymentIntent;
use App\Domains\Payments\DTOs\PaymentResult;

/**
 * Agregateur de paiement (Stripe, PayPal…).
 *
 * Le domaine ne connait ni Stripe ni PayPal — il connait une intention
 * d'encaissement et un resultat. Changer de prestataire consiste a ecrire une
 * classe et a changer une ligne de `config/promptory.php` : aucun service
 * metier, aucun controleur n'est touche.
 */
interface PaymentGatewayContract
{
    /** Identifiant du pilote, archive avec chaque vente/abonnement. */
    public function name(): string;

    public function charge(PaymentIntent $intent): PaymentResult;
}
