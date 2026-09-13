<?php

namespace App\Domains\Audit\Services;

use App\Domains\Audit\Contracts\ActivityLogRepositoryContract;
use App\Domains\Audit\Enums\ActivityAction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Point d'entree unique du journal d'activite.
 *
 * Chaque domaine metier (Prompts, Sales…) appelle `record()` au lieu d'ecrire
 * directement dans `activity_log` : le format d'un evenement ne change qu'a
 * un seul endroit, et une panne d'ecriture du journal ne doit jamais faire
 * echouer l'action metier qu'elle observe.
 */
final class ActivityLogger
{
    public function __construct(private readonly ActivityLogRepositoryContract $logs) {}

    public function record(?int $userId, ?int $promptId, ActivityAction $action): void
    {
        try {
            $this->logs->record($userId, $promptId, $action);
        } catch (\Throwable $e) {
            // Le journal d'activite est un outil d'analyse, pas une garantie
            // transactionnelle : un achat reussi ne doit pas devenir un echec
            // HTTP parce que sa trace n'a pas pu s'ecrire.
            report($e);
        }
    }

    /** @param  array<string, mixed>  $filters */
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->logs->paginate($filters, $perPage);
    }
}
