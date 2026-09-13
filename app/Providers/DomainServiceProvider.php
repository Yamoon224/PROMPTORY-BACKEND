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

/**
 * Point unique de cablage entre contrats et implementations (inversion des
 * dependances, principe D de SOLID).
 *
 * Aucun service metier ne reference une classe concrete de persistance : tout
 * passe par les interfaces listees ici. C'est ce qui permet de substituer une
 * implementation en test sans toucher au domaine.
 *
 * Le paiement suit une regle a part : deux moyens de paiement au choix de
 * l'acheteur (Stripe, PayPal) ne peuvent pas se resoudre a un seul agregateur
 * lie une fois pour toutes au demarrage. `PaymentGatewayResolver` (voir le
 * domaine Payments) choisit donc la classe a l'usage, par requete — il n'a pas
 * besoin d'etre declare ici, n'ayant aucune dependance a cabler.
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
        //
    }
}
