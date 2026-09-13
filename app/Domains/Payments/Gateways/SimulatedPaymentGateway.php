<?php

namespace App\Domains\Payments\Gateways;

use App\Domains\Payments\Contracts\PaymentGatewayContract;
use App\Domains\Payments\DTOs\PaymentIntent;
use App\Domains\Payments\DTOs\PaymentResult;
use App\Domains\Payments\Enums\GatewayStatus;

/**
 * Agregateur simule : pilote du poste de developpement et de la suite de
 * tests. Rejoue le cycle complet sans aucun appel reseau.
 *
 * Un jeton de paiement se terminant par `fail` echoue (carte refusee
 * simulee) : cela rend le chemin d'echec reproductible sans mock. Tout autre
 * jeton, y compris son absence, aboutit immediatement.
 */
final class SimulatedPaymentGateway implements PaymentGatewayContract
{
    private const FAILING_SUFFIX = 'fail';

    public function name(): string
    {
        return 'simulated';
    }

    public function charge(PaymentIntent $intent): PaymentResult
    {
        $reference = 'SIM-'.strtoupper(bin2hex(random_bytes(6)));

        if ($intent->paymentToken !== null && str_ends_with($intent->paymentToken, self::FAILING_SUFFIX)) {
            return new PaymentResult(
                gateway: $this->name(),
                externalReference: $reference,
                status: GatewayStatus::Failed,
                failureReason: 'Moyen de paiement refuse (simulation).',
            );
        }

        return new PaymentResult(
            gateway: $this->name(),
            externalReference: $reference,
            status: GatewayStatus::Succeeded,
        );
    }
}
