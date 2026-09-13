<?php

use App\Domains\Payments\Gateways\SimulatedPaymentGateway;

/*
|--------------------------------------------------------------------------
| Promptory
|--------------------------------------------------------------------------
|
| Parametres metier issus du cahier des charges : commission marketplace,
| tarifs des abonnements et agregateur de paiement. Regroupes ici plutot que
| disperses en constantes de classe, pour qu'un changement de tarif ne
| demande jamais une modification de code.
|
*/

return [
    /*
     | URL de base du frontend Next.js. Utilisee pour construire le lien de
     | reinitialisation de mot de passe envoye par e-mail : l'API n'a pas de
     | page web propre, ce lien doit pointer vers l'application cliente.
     */
    'frontend_url' => rtrim((string) env('FRONTEND_URL', 'http://localhost:3000'), '/'),

    /*
     | Commission prelevee sur chaque vente de prompt ou de pack, en
     | pourcentage. Le cahier des charges autorise une fourchette de 10 a 20 % ;
     | un taux unique et configurable est plus sur qu'un taux choisi a la volee
     | par l'appelant, qui ouvrirait la porte a une commission falsifiee.
     */
    'commission_rate' => (float) env('PROMPTORY_COMMISSION_RATE', 15.0),

    'subscriptions' => [
        'creator_premium' => [
            'price' => (float) env('PROMPTORY_CREATOR_PREMIUM_PRICE', 9.99),
            'duration_days' => 30,
        ],
        'extension_premium' => [
            'price' => (float) env('PROMPTORY_EXTENSION_PREMIUM_PRICE', 4.99),
            'duration_days' => 30,
        ],
    ],

    /*
     | Agregateur de paiement actif. `simulated` rejoue le cycle complet sans
     | appel reseau : c'est celui du poste de developpement et de la suite de
     | tests. Brancher Stripe ou PayPal consiste a ajouter une classe ici, pas
     | a toucher au domaine Sales ou Subscriptions.
     */
    'gateway' => env('PAYMENT_GATEWAY', 'simulated'),

    'gateways' => [
        'simulated' => [
            'driver' => SimulatedPaymentGateway::class,
        ],
    ],
];
