<?php

namespace App\Domains\Payments\Services;

use App\Domains\Payments\Contracts\PaymentGatewayContract;
use App\Domains\Payments\Enums\PaymentMethod;
use RuntimeException;

/**
 * Choisit l'agregateur de paiement a l'usage, plutot qu'une fois pour toutes
 * au demarrage.
 *
 * Le cahier des charges impose deux moyens de paiement au choix de
 * l'acheteur (Stripe, PayPal) : un unique agregateur lie par conteneur au
 * demarrage (comme le fait un site a moyen de paiement unique) ne peut pas
 * exprimer ce choix. Chaque achat ou abonnement resout donc son agregateur
 * ici, par le moyen de paiement demande dans la requete — jamais par un nom
 * de classe en dur dans un service metier.
 */
final class PaymentGatewayResolver
{
    /** @throws RuntimeException si aucune classe n'est configuree pour ce moyen de paiement */
    public function resolve(PaymentMethod $method): PaymentGatewayContract
    {
        $driver = config("promptory.gateways.{$method->value}.driver");

        if (! is_string($driver) || ! class_exists($driver)) {
            throw new RuntimeException(
                "Agregateur de paiement « {$method->value} » inconnu. Verifiez config/promptory.php.",
            );
        }

        /** @var PaymentGatewayContract $gateway */
        $gateway = app($driver);

        return $gateway;
    }
}
