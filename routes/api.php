<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\EvenementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
 * throttle:X,1 = au maximum X appels par minute et par adresse IP.
 * (Si ton serveur est derrière nginx / Cloudflare, voir LISEZMOI : trustProxies.)
 */

// Inscription d'une boutique
Route::post('/onboarding', [OnboardingController::class, 'register'])->middleware('throttle:5,1');

// Pendant la saisie du nom de la boutique : "quelle adresse ? déjà pris ?" (ne crée rien)
Route::get('/onboarding/verifier', [OnboardingController::class, 'verifier'])->middleware('throttle:60,1');

// Catalogue public d'une boutique
Route::get('/shops/{slug}', [ShopController::class, 'show']);
Route::get('/shops/{slug}/infos', [ShopController::class, 'infos']);                 // bloc confiance + Pixel
Route::get('/shops/{slug}/produits/{id}', [ShopController::class, 'produit']);       // page produit partageable

// Commandes : panier + formulaire, ou bouton "WhatsApp direct"
Route::post('/commandes', [CommandeController::class, 'store'])->middleware('throttle:20,1');
Route::post('/commandes/direct', [CommandeController::class, 'direct'])->middleware('throttle:20,1');

// Journal des visites (entonnoir par canal)
Route::post('/evenements', [EvenementController::class, 'store'])->middleware('throttle:120,1');

// Compteurs de visites (ceux que tu avais déjà)
Route::post('/shops/{slug}/increment-view', [ShopController::class, 'incrementView'])->middleware('throttle:120,1');
Route::post('/products/{id}/increment-view', [ProductController::class, 'incrementView'])->middleware('throttle:120,1');
