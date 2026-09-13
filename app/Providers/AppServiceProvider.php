<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        /*
         * Les relations doivent etre chargees explicitement.
         *
         * Sans cela, un acces a une relation non chargee declenche une requete
         * silencieuse — par ligne. Sur une liste de vingt prompts qui affiche
         * son createur, ses tags et ses categories, cela fait soixante
         * requetes invisibles, et le probleme ne se voit qu'en production.
         *
         * Actif hors production uniquement : en production, une relation
         * oubliee doit degrader la performance, pas casser la page d'un
         * acheteur en train de payer.
         */
        Model::preventLazyLoading(! $this->app->isProduction());

        // Une ecriture sur un attribut absent de `$fillable` est une erreur de
        // developpement, pas une donnee a ignorer en silence.
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
    }
}
