<?php

namespace App\Domains\Marketing\Http\Controllers;

use App\Domains\Marketing\Http\Requests\SubscribeNewsletterRequest;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;

/**
 * Capture d'e-mail de la landing publique (bandeau final « Pret a monetiser
 * vos prompts ? »). Une resoumission de la meme adresse n'est pas une erreur :
 * `firstOrCreate` la rend idempotente plutot que de renvoyer un conflit.
 */
class NewsletterController extends Controller
{
    public function store(SubscribeNewsletterRequest $request): JsonResponse
    {
        NewsletterSubscriber::firstOrCreate(['email' => $request->string('email')->lower()->toString()]);

        return response()->json([
            'message' => 'Inscription confirmee. Verifiez votre boite mail pour continuer.',
        ], 201);
    }
}
