<?php

namespace App\Domains\Sales\Services;

use App\Domains\Sales\Contracts\SaleRepositoryContract;
use App\Models\Prompt;
use App\Models\User;

/**
 * Decide qui a le droit de lire le contenu integral d'un prompt.
 *
 * Isolee du reste du domaine Sales (segregation d'interface) : la route
 * publique de consultation d'un prompt ne recoit que cette lecture etroite,
 * jamais de quoi initier un paiement ou lister des ventes.
 */
final class PurchaseAccessService
{
    public function __construct(private readonly SaleRepositoryContract $sales) {}

    public function canRead(Prompt $prompt, ?User $viewer): bool
    {
        if ((float) $prompt->price === 0.0) {
            return true;
        }

        if ($viewer === null) {
            return false;
        }

        if ($viewer->id === $prompt->user_id) {
            return true;
        }

        return $this->sales->hasPurchasedPrompt($viewer->id, $prompt->id);
    }
}
