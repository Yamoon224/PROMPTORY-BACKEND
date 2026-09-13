<?php

namespace App\Domains\Sales\Services;

use App\Domains\Audit\Enums\ActivityAction;
use App\Domains\Audit\Services\ActivityLogger;
use App\Domains\Packs\Enums\PackStatus;
use App\Domains\Payments\DTOs\PaymentIntent;
use App\Domains\Payments\DTOs\PaymentResult;
use App\Domains\Payments\Enums\GatewayStatus;
use App\Domains\Payments\Enums\PaymentMethod;
use App\Domains\Payments\Services\PaymentGatewayResolver;
use App\Domains\Prompts\Enums\PromptStatus;
use App\Domains\Sales\Contracts\SaleRepositoryContract;
use App\Domains\Sales\Enums\PaymentStatus;
use App\Domains\Sales\Exceptions\AlreadyPurchasedException;
use App\Domains\Sales\Exceptions\CannotPurchaseOwnItemException;
use App\Domains\Sales\Exceptions\ItemNotPurchasableException;
use App\Domains\Sales\Exceptions\PaymentFailedException;
use App\Models\Pack;
use App\Models\Prompt;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Achat d'un prompt ou d'un pack.
 *
 * `client_reference` rend l'achat idempotent : un double clic ou une requete
 * reessayee apres coupure reseau renvoie la vente deja enregistree plutot que
 * de debiter l'acheteur une seconde fois.
 */
final class PurchaseService
{
    public function __construct(
        private readonly SaleRepositoryContract $sales,
        private readonly PaymentGatewayResolver $gateways,
        private readonly ActivityLogger $activity,
    ) {}

    /**
     * @throws CannotPurchaseOwnItemException
     * @throws ItemNotPurchasableException
     * @throws AlreadyPurchasedException
     * @throws PaymentFailedException
     */
    public function purchasePrompt(
        User $buyer,
        Prompt $prompt,
        PaymentMethod $method,
        ?string $paymentToken,
        ?string $clientReference,
    ): Sale {
        $clientReference ??= (string) Str::uuid();

        if ($existing = $this->sales->findByClientReference($clientReference)) {
            return $existing;
        }

        if ($prompt->status !== PromptStatus::Published) {
            throw ItemNotPurchasableException::make($prompt->status->value);
        }

        if ($prompt->user_id === $buyer->id) {
            throw CannotPurchaseOwnItemException::make();
        }

        if ($this->sales->hasPurchasedPrompt($buyer->id, $prompt->id)) {
            throw AlreadyPurchasedException::make();
        }

        $result = $this->charge($method, $clientReference, (float) $prompt->price, "Achat du prompt « {$prompt->title} »", $paymentToken);

        $sale = DB::transaction(fn () => $this->sales->create([
            ...$this->saleAttributes($clientReference, (float) $prompt->price, $result),
            'prompt_id' => $prompt->id,
            'buyer_id' => $buyer->id,
            'creator_id' => $prompt->user_id,
        ]));

        $this->activity->record($buyer->id, $prompt->id, ActivityAction::Purchase);

        return $sale;
    }

    /**
     * @throws CannotPurchaseOwnItemException
     * @throws ItemNotPurchasableException
     * @throws AlreadyPurchasedException
     * @throws PaymentFailedException
     */
    public function purchasePack(
        User $buyer,
        Pack $pack,
        PaymentMethod $method,
        ?string $paymentToken,
        ?string $clientReference,
    ): Sale {
        $clientReference ??= (string) Str::uuid();

        if ($existing = $this->sales->findByClientReference($clientReference)) {
            return $existing;
        }

        if ($pack->status !== PackStatus::Published) {
            throw ItemNotPurchasableException::make($pack->status->value);
        }

        if ($pack->user_id === $buyer->id) {
            throw CannotPurchaseOwnItemException::make();
        }

        if ($this->sales->hasPurchasedPack($buyer->id, $pack->id)) {
            throw AlreadyPurchasedException::make();
        }

        $result = $this->charge($method, $clientReference, (float) $pack->price, "Achat du pack « {$pack->title} »", $paymentToken);

        $sale = DB::transaction(fn () => $this->sales->create([
            ...$this->saleAttributes($clientReference, (float) $pack->price, $result),
            'pack_id' => $pack->id,
            'buyer_id' => $buyer->id,
            'creator_id' => $pack->user_id,
        ]));

        return $sale;
    }

    /** @throws PaymentFailedException */
    private function charge(PaymentMethod $method, string $reference, float $amount, string $description, ?string $paymentToken): PaymentResult
    {
        $result = $this->gateways->resolve($method)->charge(new PaymentIntent(
            reference: $reference,
            amount: $amount,
            currency: 'EUR',
            description: $description,
            paymentToken: $paymentToken,
        ));

        if ($result->status === GatewayStatus::Failed) {
            throw PaymentFailedException::make($result->failureReason ?? 'raison inconnue');
        }

        return $result;
    }

    /** @return array<string, mixed> */
    private function saleAttributes(string $clientReference, float $price, PaymentResult $result): array
    {
        $commissionRate = (float) config('promptory.commission_rate');

        return [
            'client_reference' => $clientReference,
            'price' => $price,
            'commission_rate' => $commissionRate,
            'commission' => round($price * $commissionRate / 100, 2),
            'payment_status' => $result->status === GatewayStatus::Succeeded ? PaymentStatus::Completed : PaymentStatus::Pending,
            'payment_gateway' => $result->gateway,
            'payment_reference' => $result->externalReference,
        ];
    }
}
