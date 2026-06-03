<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\Entreprise;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class CommandeController extends Controller
{
    //
     public function store(Request $request)
    {
        // 1. Validation des données envoyées par Next.js
        $validator = Validator::make($request->all(), [
            'entreprise_slug' => 'required|string',
            'nom_client' => 'required|string|max:255',
            'phone_client' => 'required|string|max:50',
            'adresse_livraison' => 'required|string',
            'montant_total' => 'required|integer',
            'produits' => 'required|array|min:1',
            'produits.*.id' => 'required|integer|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Récupérer l'entreprise (le tenant) grâce à son slug unique
        $shop = Entreprise::where('slug', $request->entreprise_slug)->first();

        if (!$shop) {
            return response()->json([
                'status' => 'error',
                'message' => 'Boutique introuvable.'
            ], 404);
        }

        // 3. Lancer une transaction de base de données pour tout sécuriser
        DB::beginTransaction();

        try {
            // Création de l'en-tête de la commande (notez l'utilisation de 'statut' conforme à votre migration)
            $commande = Commande::create([
                'entreprise_id' => $shop->id,
                'nom_client' => $request->nom_client,
                'phone_client' => $request->phone_client,
                'adresse_livraison' => $request->adresse_livraison,
                'montant_total' => $request->montant_total,
                'statut' => 'en_attente', // Aligné sur votre colonne enum 'statut'
            ]);

            // Liaison de chaque produit et décrémentation des stocks physiques
            foreach ($request->produits as $item) {
                $product = Produit::find($item['id']);

                // Vérifier la disponibilité en stock réel
                if ($product->quantite_stock < $item['quantite']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'stock_error',
                        'message' => "Le produit '{$product->designation}' est en rupture de stock."
                    ], 400);
                }

                // Insertion dans la table pivot 'commande_produit'
                $commande->produits()->attach($product->id, [
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix_unitaire']
                ]);

                // Décrémentation physique immédiate de la quantité de stock du produit
                $product->decrement('quantite_stock', $item['quantite']);
            }

            // Tout s'est bien passé : validation définitive des écritures SQL
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Commande enregistrée avec succès en base de données !',
                'commande_id' => $commande->id
            ], 201);

        } catch (\Exception $e) {
            // En cas de bug inattendu, on annule tout pour éviter les données orphelines
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur technique lors de l\'enregistrement de la commande.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
