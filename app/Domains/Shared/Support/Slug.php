<?php

namespace App\Domains\Shared\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Slug unique pour une ressource publique (prompt, pack, categorie…).
 *
 * Genere une seule fois, a la creation : le renommer ensuite casserait
 * silencieusement chaque lien deja partage. Un suffixe numerique departage
 * les collisions plutot que de rejeter la creation.
 */
final class Slug
{
    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query  requete fraiche sur le modele cible (`Prompt::query()`…)
     */
    public static function unique(Builder $query, string $from, string $column = 'slug'): string
    {
        $base = Str::slug($from) ?: 'item';
        $slug = $base;
        $suffix = 2;

        while ((clone $query)->where($column, $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
