<?php

namespace App\Domains\Payments\Gateways;

use App\Domains\Payments\Contracts\PaymentGatewayContract;
use App\Domains\Payments\DTOs\PaymentIntent;
use App\Domains\Payments\DTOs\PaymentResult;
use App\Domains\Payments\Enums\GatewayStatus;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Paiement par carte via l'API Stripe (PaymentIntents).
 *
 * `paymentToken` est l'identifiant d'un `PaymentMethod` Stripe (`pm_…`) cree
 * cote client par Stripe.js — jamais un numero de carte, qui ne doit a aucun
 * moment transiter par notre backend (portee PCI-DSS).
 *
 * **Sans cle secrete configuree** (`STRIPE_SECRET`), aucun appel reseau n'est
 * fait : le paiement est simule, pour que le poste de developpement et la
 * demonstration fonctionnent sans compte Stripe. Une cle de test suffit a
 * basculer sur de vrais appels, en mode bac a sable Stripe.
 */
final class StripePaymentGateway implements PaymentGatewayContract
{
    private const FAILING_SUFFIX = 'fail';

    public function name(): string
    {
        return 'stripe';
    }

    public function charge(PaymentIntent $intent): PaymentResult
    {
        $secretKey = (string) config('services.stripe.secret');

        if ($secretKey === '') {
            return $this->simulate($intent);
        }

        try {
            $response = Http::asForm()
                ->withToken($secretKey)
                ->post('https://api.stripe.com/v1/payment_intents', [
                    'amount' => (int) round($intent->amount * 100),
                    'currency' => strtolower($intent->currency),
                    'description' => $intent->description,
                    'payment_method' => $intent->paymentToken,
                    'confirm' => 'true',
                    'automatic_payment_methods[enabled]' => 'true',
                    'automatic_payment_methods[allow_redirects]' => 'never',
                    'metadata[reference]' => $intent->reference,
                ]);

            if ($response->failed()) {
                return new PaymentResult(
                    gateway: $this->name(),
                    externalReference: $intent->reference,
                    status: GatewayStatus::Failed,
                    failureReason: (string) $response->json('error.message', 'Stripe a refuse le paiement.'),
                );
            }

            $status = (string) $response->json('status');

            return new PaymentResult(
                gateway: $this->name(),
                externalReference: (string) $response->json('id', $intent->reference),
                status: $status === 'succeeded' ? GatewayStatus::Succeeded : GatewayStatus::Pending,
            );
        } catch (Throwable $exception) {
            report($exception);

            return new PaymentResult(
                gateway: $this->name(),
                externalReference: $intent->reference,
                status: GatewayStatus::Failed,
                failureReason: 'Stripe est injoignable pour le moment.',
            );
        }
    }

    /**
     * Rejoue le meme cycle que `SimulatedPaymentGateway` : un jeton se
     * terminant par `fail` echoue, tout le reste aboutit immediatement.
     */
    private function simulate(PaymentIntent $intent): PaymentResult
    {
        $reference = 'stripe_sim_'.strtoupper(bin2hex(random_bytes(6)));
        $isFailing = $intent->paymentToken !== null && str_ends_with($intent->paymentToken, self::FAILING_SUFFIX);

        return new PaymentResult(
            gateway: $this->name(),
            externalReference: $reference,
            status: $isFailing ? GatewayStatus::Failed : GatewayStatus::Succeeded,
            failureReason: $isFailing ? 'Carte refusee (simulation — aucune cle Stripe configuree).' : null,
        );
    }
}
