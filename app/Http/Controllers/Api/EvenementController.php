<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Journal très simple de ce que font les visiteurs, pour répondre à la question :
 * "où les gens s'arrêtent-ils ?"
 *
 *   visite_boutique -> vue_produit -> ajout_panier -> (commande : déjà dans la table commandes)
 *
 * Le champ "canal" dit d'où vient le visiteur (facebook, snapchat, whatsapp...).
 */
class EvenementController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'entreprise_slug' => 'required|string|max:255',
            'type'            => 'required|in:visite_boutique,vue_produit,ajout_panier',
            'canal'           => ['nullable', 'string', 'max:30', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'produit_id'      => 'nullable|integer',
        ]);

        $shop = Entreprise::where('slug', $data['entreprise_slug'])->first();

        if (! $shop) {
            return response()->json(['ok' => false], 404);
        }

        DB::table('evenements')->insert([
            'entreprise_id' => $shop->id,
            'produit_id'    => $data['produit_id'] ?? null,
            'type'          => $data['type'],
            'canal'         => isset($data['canal']) ? strtolower($data['canal']) : null,
            'created_at'    => now(),
        ]);

        return response()->json(['ok' => true], 201);
    }
}
