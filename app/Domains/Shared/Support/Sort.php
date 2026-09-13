<?php

namespace App\Domains\Shared\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Tri d'une liste demande par le client.
 *
 * Le nom de colonne ne vient jamais de la requete : le client envoie une **cle
 * publique** (`title`, `price`…) que chaque depot traduit lui-meme, via une
 * allowlist, en colonne ou en expression SQL. C'est la seule facon de proposer
 * un tri par en-tete sans ouvrir une injection SQL.
 */
final class Sort
{
    /**
     * Marque une expression SQL, par opposition a un simple nom de colonne.
     * L'expression provient toujours d'une constante de depot, jamais de la
     * requete : c'est ce qui la rend sure a interpoler.
     *
     * @return array{0: string, 1: string}
     */
    public static function raw(string $expression): array
    {
        return ['raw', $expression];
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, mixed>  $filters  contient eventuellement `sort` et `direction`
     * @param  array<string, string|array{0: string, 1: string}>  $allowed  cle publique vers colonne, ou Sort::raw(...)
     * @return Builder<TModel>
     */
    public static function apply(
        Builder $query,
        array $filters,
        array $allowed,
        string $fallbackColumn = 'id',
        string $fallbackDirection = 'desc',
    ): Builder {
        $direction = strtolower((string) ($filters['direction'] ?? '')) === 'desc' ? 'desc' : 'asc';
        $key = $filters['sort'] ?? null;

        // Une cle inconnue est ignoree plutot que refusee : un tri est un
        // confort d'affichage, pas une instruction dont l'echec doit couter
        // une page d'erreur a l'utilisateur.
        if (! is_string($key) || ! array_key_exists($key, $allowed)) {
            return $query->orderBy($fallbackColumn, $fallbackDirection);
        }

        $target = $allowed[$key];

        if (is_array($target)) {
            return $query->orderByRaw($target[1].' '.$direction)->orderBy('id', 'desc');
        }

        // Un tri par colonne non unique laisserait des lignes se croiser d'une
        // page a l'autre : on departage toujours par identifiant.
        return $query->orderBy($target, $direction)->orderBy('id', 'desc');
    }
}
