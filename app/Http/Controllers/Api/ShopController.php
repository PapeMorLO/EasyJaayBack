<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entreprise; 

class ShopController extends Controller
{
    //
    public function show($slug)
    {
        // On récupère la boutique avec son catalogue filtré
        $shop = Entreprise::where('slug', $slug)
            ->where('is_active', true)
            ->with(['produits' => function($query) {
                // Uniquement les produits en stock
                $query->where('quantite_stock', '>', 0);
            }])
            ->first();

        // Gestion de l'erreur 404 si la boutique n'existe pas ou est inactive
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
    }
}
