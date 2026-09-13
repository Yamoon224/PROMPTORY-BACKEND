<?php

namespace App\Providers;

use App\Domains\Audit\Contracts\ActivityLogRepositoryContract;
use App\Domains\Audit\Repositories\EloquentActivityLogRepository;
use App\Domains\Catalog\Contracts\CategoryRepositoryContract;
use App\Domains\Catalog\Contracts\IaModelRepositoryContract;
use App\Domains\Catalog\Contracts\TagRepositoryContract;
use App\Domains\Catalog\Repositories\EloquentCategoryRepository;
use App\Domains\Catalog\Repositories\EloquentIaModelRepository;
use App\Domains\Catalog\Repositories\EloquentTagRepository;
use App\Domains\Packs\Contracts\PackRepositoryContract;
use App\Domains\Packs\Repositories\EloquentPackRepository;
use App\Domains\Payments\Contracts\PaymentGatewayContract;
use App\Domains\Prompts\Contracts\FolderRepositoryContract;
use App\Domains\Prompts\Contracts\PromptRepositoryContract;
use App\Domains\Prompts\Repositories\EloquentFolderRepository;
use App\Domains\Prompts\Repositories\EloquentPromptRepository;
use App\Domains\Reviews\Contracts\ReviewRepositoryContract;
use App\Domains\Reviews\Repositories\EloquentReviewRepository;
use App\Domains\Sales\Contracts\SaleRepositoryContract;
use App\Domains\Sales\Repositories\EloquentSaleRepository;
use App\Domains\Subscriptions\Contracts\SubscriptionRepositoryContract;
use App\Domains\Subscriptions\Repositories\EloquentSubscriptionRepository;
use App\Domains\Users\Contracts\UserRepositoryContract;
use App\Domains\Users\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

/**
 * Point unique de cablage entre contrats et implementations (inversion des
 * dependances, principe D de SOLID).
 *
 * Aucun service metier ne reference une classe concrete de persistance ni de
 * prestataire de paiement : tout passe par les interfaces listees ici. C'est
 * ce qui permet de substituer une implementation en test, ou de changer
 * d'agregateur de paiement, en touchant ce seul fichier.
 */
class DomainServiceProvider extends ServiceProvider
{
    /**
     * Contrats de persistance et leurs implementations Eloquent.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        // --- Comptes et audit ---------------------------------------------
        UserRepositoryContract::class => EloquentUserRepository::class,
        ActivityLogRepositoryContract::class => EloquentActivityLogRepository::class,

        // --- Referentiel marketplace ---------------------------------------
        CategoryRepositoryContract::class => EloquentCategoryRepository::class,
        TagRepositoryContract::class => EloquentTagRepository::class,
        IaModelRepositoryContract::class => EloquentIaModelRepository::class,

        // --- Prompts ---------------------------------------------------------
        FolderRepositoryContract::class => EloquentFolderRepository::class,
        PromptRepositoryContract::class => EloquentPromptRepository::class,

        // --- Packs -------------------------------------------------------------
        PackRepositoryContract::class => EloquentPackRepository::class,

        // --- Transactions ------------------------------------------------------
        SaleRepositoryContract::class => EloquentSaleRepository::class,
        SubscriptionRepositoryContract::class => EloquentSubscriptionRepository::class,

        // --- Avis ----------------------------------------------------------
        ReviewRepositoryContract::class => EloquentReviewRepository::class,
    ];

    public function register(): void
    {
        $this->registerPaymentGateway();
    }

    /**
     * Agregateur de paiement, choisi par configuration.
     *
     * Un pilote inconnu leve immediatement plutot que de retomber
     * silencieusement sur la simulation : une plateforme qui croit encaisser
     * alors qu'elle simule est le pire scenario imaginable, et il ne se
     * decouvrirait qu'au premier rapprochement comptable.
     */
    private function registerPaymentGateway(): void
    {
        $this->app->singleton(PaymentGatewayContract::class, function (): PaymentGatewayContract {
            $name = (string) config('promptory.gateway');
            $driver = config("promptory.gateways.{$name}.driver");

            if (! is_string($driver) || ! class_exists($driver)) {
                throw new RuntimeException(
                    "Agregateur de paiement « {$name} » inconnu. Verifiez PAYMENT_GATEWAY et config/promptory.php.",
                );
            }

            /** @var PaymentGatewayContract $gateway */
            $gateway = $this->app->make($driver);

            return $gateway;
        });
    }
}
