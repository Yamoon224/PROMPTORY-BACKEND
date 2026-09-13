<?php

namespace App\Domains\Subscriptions\Services;

use App\Domains\Payments\DTOs\PaymentIntent;
use App\Domains\Payments\Enums\GatewayStatus;
use App\Domains\Payments\Enums\PaymentMethod;
use App\Domains\Payments\Services\PaymentGatewayResolver;
use App\Domains\Sales\Exceptions\PaymentFailedException;
use App\Domains\Shared\Exceptions\OwnershipViolationException;
use App\Domains\Subscriptions\Contracts\SubscriptionRepositoryContract;
use App\Domains\Subscriptions\Enums\SubscriptionStatus;
use App\Domains\Subscriptions\Enums\SubscriptionType;
use App\Domains\Subscriptions\Exceptions\AlreadySubscribedException;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Abonnements premium : createur (fonctionnalites avancees, statistiques,
 * visibilite) et extension Chrome (organisation, synchronisation cloud).
 *
 * Tarifs et duree lus depuis `config/promptory.php`, jamais depuis la
 * requete : un prix cote client serait un prix a la carte pour quiconque
 * inspecte la requete.
 */
final class SubscriptionService
{
    public function __construct(
        private readonly SubscriptionRepositoryContract $subscriptions,
        private readonly PaymentGatewayResolver $gateways,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Subscription>
     */
    public function listForUser(int $userId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->subscriptions->paginateForUser($userId, $filters, $perPage);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Subscription>
     */
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->subscriptions->paginate($filters, $perPage);
    }

    /**
     * @throws AlreadySubscribedException
     * @throws PaymentFailedException
     */
    public function subscribe(User $user, SubscriptionType $type, PaymentMethod $method, ?string $paymentToken): Subscription
    {
        if ($this->subscriptions->findActive($user->id, $type) !== null) {
            throw AlreadySubscribedException::make($type->value);
        }

        $plan = (array) config("promptory.subscriptions.{$type->value}");
        $price = (float) $plan['price'];
        $durationDays = (int) $plan['duration_days'];

        $result = $this->gateways->resolve($method)->charge(new PaymentIntent(
            reference: sprintf('sub-%d-%s-%s', $user->id, $type->value, now()->timestamp),
            amount: $price,
            currency: 'EUR',
            description: "Abonnement {$type->value}",
            paymentToken: $paymentToken,
        ));

        if ($result->status === GatewayStatus::Failed) {
            throw PaymentFailedException::make($result->failureReason ?? 'raison inconnue');
        }

        return DB::transaction(fn () => $this->subscriptions->create([
            'user_id' => $user->id,
            'type' => $type,
            'price' => $price,
            'status' => SubscriptionStatus::Active,
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays($durationDays),
            'payment_gateway' => $result->gateway,
            'payment_reference' => $result->externalReference,
        ]));
    }

    /** @throws OwnershipViolationException */
    public function cancel(Subscription $subscription, int $userId): Subscription
    {
        if ($subscription->user_id !== $userId) {
            throw OwnershipViolationException::make();
        }

        return $this->subscriptions->update($subscription, ['status' => SubscriptionStatus::Canceled]);
    }
}
