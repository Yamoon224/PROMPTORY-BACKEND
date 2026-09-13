<?php

use App\Domains\Audit\Http\Controllers\ActivityLogController;
use App\Domains\Auth\Http\Controllers\AuthController;
use App\Domains\Shared\Http\Controllers\HealthController;
use App\Domains\Catalog\Http\Controllers\CategoryController;
use App\Domains\Catalog\Http\Controllers\IaModelController;
use App\Domains\Catalog\Http\Controllers\TagController;
use App\Domains\Packs\Http\Controllers\PackController;
use App\Domains\Packs\Http\Controllers\PackModerationController;
use App\Domains\Prompts\Http\Controllers\FolderController;
use App\Domains\Prompts\Http\Controllers\PromptController;
use App\Domains\Prompts\Http\Controllers\PromptModerationController;
use App\Domains\Reviews\Http\Controllers\ReviewController;
use App\Domains\Sales\Http\Controllers\PurchaseController;
use App\Domains\Sales\Http\Controllers\SaleController;
use App\Domains\Subscriptions\Http\Controllers\SubscriptionController;
use App\Domains\Users\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
|
| Les routes publiques (marketplace, authentification) n'exigent aucun jeton.
| Toutes les autres exigent un jeton Sanctum et, pour la moderation et le
| back-office, la permission correspondante.
|
*/

Route::get('/health', HealthController::class);

// --- Authentification -------------------------------------------------------

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Marketplace publique -----------------------------------------------------

Route::get('/prompts', [PromptController::class, 'index']);
Route::get('/prompts/{slug}', [PromptController::class, 'show']);
Route::get('/prompts/id/{prompt}/reviews', [ReviewController::class, 'index']);
Route::get('/packs', [PackController::class, 'index']);
Route::get('/packs/{slug}', [PackController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/ia-models', [IaModelController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    // --- Compte -----------------------------------------------------------
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- Mes prompts (dashboard createur) ----------------------------------
    //
    // Le parametre est explicitement lie par identifiant (`{prompt:id}`) :
    // `Prompt::getRouteKeyName()` vaut `slug` pour que la fiche publique se
    // resolve par slug, mais ces routes viennent d'une action du dashboard
    // (un bouton « modifier » sur une ligne connue par son id), pas d'une URL
    // tapee a la main.
    Route::get('/my/prompts', [PromptController::class, 'mine']);
    Route::post('/prompts', [PromptController::class, 'store']);
    Route::put('/prompts/{prompt:id}', [PromptController::class, 'update']);
    Route::delete('/prompts/{prompt:id}', [PromptController::class, 'destroy']);
    Route::post('/prompts/{prompt:id}/submit', [PromptModerationController::class, 'submit']);
    Route::post('/prompts/{prompt:id}/archive', [PromptModerationController::class, 'archive']);
    Route::post('/prompts/{prompt:id}/unarchive', [PromptModerationController::class, 'unarchive']);
    Route::post('/prompts/id/{prompt}/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // --- Mes dossiers -------------------------------------------------------
    Route::apiResource('folders', FolderController::class)->except('show');

    // --- Mes packs -----------------------------------------------------------
    Route::get('/my/packs', [PackController::class, 'mine']);
    Route::post('/packs', [PackController::class, 'store']);
    Route::put('/packs/{pack:id}', [PackController::class, 'update']);
    Route::delete('/packs/{pack:id}', [PackController::class, 'destroy']);
    Route::post('/packs/{pack:id}/archive', [PackModerationController::class, 'archive']);
    Route::post('/packs/{pack:id}/resubmit', [PackModerationController::class, 'resubmit']);

    // --- Achats ---------------------------------------------------------
    Route::post('/prompts/{prompt:id}/purchase', [PurchaseController::class, 'purchasePrompt']);
    Route::post('/packs/{pack:id}/purchase', [PurchaseController::class, 'purchasePack']);
    Route::get('/my/purchases', [SaleController::class, 'purchases']);
    Route::get('/my/earnings', [SaleController::class, 'earnings']);

    // --- Abonnements ---------------------------------------------------------
    Route::get('/my/subscriptions', [SubscriptionController::class, 'mine']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);

    // --- Moderation (permission dediee) --------------------------------------
    Route::middleware('permission:prompts.moderate')->group(function () {
        Route::get('/moderation/prompts', [PromptModerationController::class, 'pending']);
        Route::post('/moderation/prompts/{prompt:id}/approve', [PromptModerationController::class, 'approve']);
        Route::post('/moderation/prompts/{prompt:id}/reject', [PromptModerationController::class, 'reject']);
        Route::get('/moderation/packs', [PackModerationController::class, 'pending']);
        Route::post('/moderation/packs/{pack:id}/approve', [PackModerationController::class, 'approve']);
        Route::post('/moderation/packs/{pack:id}/reject', [PackModerationController::class, 'reject']);
    });

    // --- Back-office (permission dediee) -------------------------------------
    Route::middleware('permission:platform.manage')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('categories', CategoryController::class)->except('index');
        Route::apiResource('tags', TagController::class)->except('index');
        Route::apiResource('ia-models', IaModelController::class)->except('index')->parameters(['ia-models' => 'ia_model']);
        Route::get('/sales', [SaleController::class, 'index']);
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);
        Route::get('/activity-log', [ActivityLogController::class, 'index']);
    });
});
