<?php

namespace App\Domains\Payments\Gateways;

use App\Domains\Payments\Contracts\PaymentGatewayContract;
use App\Domains\Payments\DTOs\PaymentIntent;
use App\Domains\Payments\DTOs\PaymentResult;
use App\Domains\Payments\Enums\GatewayStatus;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Paiement PayPal (Orders v2).
 *
 * `paymentToken` est l'identifiant de la commande PayPal deja approuvee par
 * l'acheteur cote client (PayPal JS SDK) : ce gateway ne fait que la
 * capturer, jamais que la creer — l'approbation ne peut se faire que dans le
 * navigateur de l'acheteur, aupres de PayPal lui-meme.
 *
 * **Sans identifiants configures** (`PAYPAL_CLIENT_ID`/`PAYPAL_SECRET`), le
 * paiement est simule, pour que le poste de developpement fonctionne sans
 * compte marchand PayPal.
 */
final class PaypalPaymentGateway implements PaymentGatewayContract
{
    private const FAILING_SUFFIX = 'fail';

    public function name(): string
    {
        return 'paypal';
    }

    public function charge(PaymentIntent $intent): PaymentResult
    {
        $clientId = (string) config('services.paypal.client_id');
        $secret = (string) config('services.paypal.secret');

        if ($clientId === '' || $secret === '') {
            return $this->simulate($intent);
        }

        $orderId = $intent->paymentToken;

        if ($orderId === null || $orderId === '') {
            return new PaymentResult(
                gateway: $this->name(),
                externalReference: $intent->reference,
                status: GatewayStatus::Failed,
                failureReason: 'Aucune commande PayPal approuvee a capturer.',
            );
        }

        try {
            $baseUrl = $this->baseUrl();

            $tokenResponse = Http::asForm()
                ->withBasicAuth($clientId, $secret)
                ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

            if ($tokenResponse->failed()) {
                return new PaymentResult(
                    gateway: $this->name(),
                    externalReference: $orderId,
                    status: GatewayStatus::Failed,
                    failureReason: 'Authentification PayPal impossible.',
                );
            }

            $accessToken = (string) $tokenResponse->json('access_token');

            $captureResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if ($captureResponse->failed()) {
                return new PaymentResult(
                    gateway: $this->name(),
                    externalReference: $orderId,
                    status: GatewayStatus::Failed,
                    failureReason: (string) $captureResponse->json('message', 'PayPal a refuse la capture.'),
                );
            }

            $status = (string) $captureResponse->json('status');

            return new PaymentResult(
                gateway: $this->name(),
                externalReference: $orderId,
                status: $status === 'COMPLETED' ? GatewayStatus::Succeeded : GatewayStatus::Pending,
            );
        } catch (Throwable $exception) {
            report($exception);

            return new PaymentResult(
                gateway: $this->name(),
                externalReference: $orderId,
                status: GatewayStatus::Failed,
                failureReason: 'PayPal est injoignable pour le moment.',
            );
        }
    }

    private function baseUrl(): string
    {
        return config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Rejoue le meme cycle que `SimulatedPaymentGateway` : un jeton se
     * terminant par `fail` echoue, tout le reste aboutit immediatement.
     */
    private function simulate(PaymentIntent $intent): PaymentResult
    {
        $reference = 'paypal_sim_'.strtoupper(bin2hex(random_bytes(6)));
        $isFailing = $intent->paymentToken !== null && str_ends_with($intent->paymentToken, self::FAILING_SUFFIX);

        return new PaymentResult(
            gateway: $this->name(),
            externalReference: $reference,
            status: $isFailing ? GatewayStatus::Failed : GatewayStatus::Succeeded,
            failureReason: $isFailing ? 'Paiement refuse (simulation — aucun compte PayPal configure).' : null,
        );
    }
}
