<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\ProductController; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/onboarding', [OnboardingController::class, 'register']);


// Route pour récupérer le catalogue d'une boutique spécifique via son slug
Route::get('/shops/{slug}', [ShopController::class, 'show']);

// Route COMMANDES : Enregistre la commande
Route::post('/commandes', [CommandeController::class, 'store']);

Route::post('/shops/{slug}/increment-view', [ShopController::class, 'incrementView']);
Route::post('/products/{id}/increment-view', [ProductController::class, 'incrementView']);

/*
// Route pour récupérer le catalogue d'une boutique spécifique via son slug
Route::get('/shops/{slug}', function ($slug) {
    $shop = Entreprise::where('slug', $slug)
        ->where('is_active', true)
        ->with(['produits' => function($query) {
            $query->where('quantite_stock', '>', 0); // Uniquement les produits en stock
        }])
        ->first();

    if (!$shop) {
        return response()->json(['message' => 'Boutique introuvable.'], 404);
    }

    return response()->json([
        'status' => 'success',
        'shop' => [
            'id' => $shop->id,
            'raison_sociale' => $shop->raison_sociale,
            'slug' => $shop->slug,
            'theme' => $shop->couleur_theme ?? '#ec4899',
            'contact_whatsapp' => $shop->contact_whatsapp,
        ],
        'produits' => $shop->produits
    ]);
});
*/