<?php

namespace Database\Seeders;

use App\Domains\Subscriptions\Enums\SubscriptionStatus;
use App\Domains\Subscriptions\Enums\SubscriptionType;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Abonnements premium de demonstration : createur et extension Chrome, sur
 * une partie des comptes existants, avec un melange de statuts (actif,
 * annule, expire) pour que « Mon abonnement » et le back-office
 * « Abonnements » ne soient pas vides ni tous identiques des la premiere
 * connexion.
 *
 * Independant de `DatabaseSeeder::run()` : il repart des comptes deja en
 * base (role `user`) plutot que d'une collection en memoire, pour pouvoir
 * tourner seul (`php artisan db:seed --class=SubscriptionSeeder`) sur une
 * base deja peuplee sans recreer d'utilisateurs ni echouer sur un e-mail en
 * doublon.
 */
class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::role('user')->orderBy('id')->get();

        if ($users->count() < 2) {
            $this->command?->warn('Pas assez de comptes "user" en base : lancez DatabaseSeeder au prealable.');

            return;
        }

        $creatorPremiumPrice = (float) config('promptory.subscriptions.creator_premium.price');
        $extensionPremiumPrice = (float) config('promptory.subscriptions.extension_premium.price');

        // Les dix premiers comptes (dont le createur de demo, cree en tout
        // premier) recoivent l'abonnement createur. `DatabaseSeeder` cree ses
        // vingt createurs avant ses quinze acheteurs : les huit comptes
        // suivant ce premier groupe de vingt (dont l'acheteur de demo)
        // recoivent l'extension Chrome. Sur une base plus petite ou sans ce
        // decoupage, les deux groupes se recoupent plutot que de rester vides.
        $creatorPool = $users->take(10);
        $buyerPool = $users->slice(20, 8)->values();
        if ($buyerPool->isEmpty()) {
            $buyerPool = $users->slice(10, 8)->values();
        }
        if ($buyerPool->isEmpty()) {
            $buyerPool = $users->take(8);
        }

        foreach ($creatorPool as $index => $user) {
            $this->createSubscription($user, SubscriptionType::CreatorPremium, $creatorPremiumPrice, match (true) {
                $index < 7 => SubscriptionStatus::Active,
                $index < 9 => SubscriptionStatus::Canceled,
                default => SubscriptionStatus::Expired,
            });
        }

        foreach ($buyerPool as $index => $user) {
            $this->createSubscription(
                $user,
                SubscriptionType::ExtensionPremium,
                $extensionPremiumPrice,
                $index < 6 ? SubscriptionStatus::Active : SubscriptionStatus::Canceled,
            );
        }
    }

    private function createSubscription(User $user, SubscriptionType $type, float $price, SubscriptionStatus $status): void
    {
        // Idempotent : rejouer le seeder ne duplique pas les abonnements deja en place.
        if (Subscription::query()->where('user_id', $user->id)->where('type', $type)->exists()) {
            return;
        }

        $startDate = $status === SubscriptionStatus::Expired
            ? now()->subDays(60)
            : now()->subDays(random_int(1, 25));

        Subscription::create([
            'user_id' => $user->id,
            'type' => $type,
            'price' => $price,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addDays(30),
            'payment_gateway' => fake()->randomElement(['stripe', 'paypal']),
            'payment_reference' => 'sim_'.Str::uuid(),
        ]);
    }
}
