<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
     | Sans cle configuree, `StripePaymentGateway` rejoue le meme cycle que
     | l'agregateur simule (voir sa methode `simulate()`) : le poste de
     | developpement fonctionne sans compte Stripe. Une cle de test (`sk_test_…`)
     | suffit a basculer sur de vrais appels a l'API Stripe, en mode bac a sable.
     */
    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
    ],

    /*
     | Memes garanties que Stripe ci-dessus : sans identifiants, `PaypalPaymentGateway`
     | simule le paiement plutot que d'appeler l'API PayPal.
     */
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
    ],

];
