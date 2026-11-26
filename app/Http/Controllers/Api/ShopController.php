<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use App\Models\Produit;

class ShopController extends Controller
{
    // Les colonnes d'un produit qu'on a le droit d'envoyer au public (jamais prix_achat)
    private const COLONNES_PRODUIT = ['id', 'designation', 'description', 'prix_vente', 'taux_promotion',
                                      'quantite_stock', 'color', 'taille_dimension', 'image1'];

    /**
     * Ce que voit la boutique publique (Next.js).
     * On envoie UNIQUEMENT ce qui est utile au visiteur : jamais le prix d'achat, jamais les brouillons.
     */
    public function show($slug)
    {
        $shop = $this->boutiqueActive($slug);

        $produits = Produit::where('entreprise_id', $shop->id)
            ->where('etat', 'publie')     // pas de brouillons
            ->orderBy('id')
            ->get(self::COLONNES_PRODUIT)
            ->map(fn (Produit $p) => $this->produitPublic($p));

        return response()->json([
            'status'   => 'success',
            'shop'     => $this->boutiquePublique($shop) + $this->infosPubliques($shop),
            'produits' => $produits,
        ]);
    }

    /**
     * Un seul produit (page partageable /{boutique}/p/{id}).
     * Un brouillon ou un produit d'une autre boutique donne 404.
     */
    public function produit($slug, $id)
    {
        $shop = $this->boutiqueActive($slug);

        $produit = Produit::where('entreprise_id', $shop->id)
            ->where('etat', 'publie')
            ->where('id', $id)
            ->first(self::COLONNES_PRODUIT);

        if (! $produit) {
            return response()->json(['message' => 'Produit introuvable.'], 404);
        }

        return response()->json([
            'status'  => 'success',
            'shop'    => $this->boutiquePublique($shop),
            'produit' => $this->produitPublic($produit),
        ]);
    }

    /**
     * Le bloc "confiance" : adresse, paiements, livraison, contacts, réseaux, Pixel.
     * Petite réponse séparée pour que le front puisse l'afficher sur n'importe quelle page.
     */
    public function infos($slug)
    {
        $shop = $this->boutiqueActive($slug);

        return response()->json([
            'status' => 'success',
            'infos'  => $this->infosPubliques($shop) + [
                'raison_sociale'   => $shop->raison_sociale,
                'contact_whatsapp' => $shop->contact_whatsapp,
                'contact_call'     => $shop->contact_call,
                'adresse'          => $shop->adresse,
                'type_livraison'   => $shop->type_livraison,
            ],
        ]);
    }

    public function incrementView($slug)
    {
        $shop = Entreprise::where('slug', $slug)->firstOrFail();
        $shop->increment('visites');
        return response()->json(['success' => true]);
    }

    // ---------------------------------------------------------------------

    private function boutiqueActive(string $slug): Entreprise
    {
        // firstOrFail -> 404 automatique en JSON
        return Entreprise::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    private function boutiquePublique(Entreprise $shop): array
    {
        return [
            'id'               => $shop->id,
            'logo'             => $shop->logo,
            'raison_sociale'   => $shop->raison_sociale,
            'slug'             => $shop->slug,
            'couleur_theme'    => $shop->couleur_theme ?? '#10b981',
            'theme'            => $shop->couleur_theme ?? '#10b981',   // ancien nom, gardé pour compatibilité
            'contact_whatsapp' => $shop->contact_whatsapp,
            'contact_call'     => $shop->contact_call,
            'adresse'          => $shop->adresse,
            'site_web'         => $shop->site_web,
            'type_livraison'   => $shop->type_livraison,
        ];
    }

    private function infosPubliques(Entreprise $shop): array
    {
        return [
            'a_propos'        => $shop->a_propos,
            'infos_livraison' => $shop->infos_livraison,
            'paiements'       => $shop->paiements ?? [],
            'facebook_url'    => $shop->facebook_url,
            'instagram_url'   => $shop->instagram_url,
            'snapchat_url'    => $shop->snapchat_url,
            'tiktok_url'      => $shop->tiktok_url,
            'pixel_facebook'  => $shop->pixel_facebook,
        ];
    }

    private function produitPublic(Produit $p): array
    {
        $enPromo = $p->taux_promotion > 0;

        return [
            'id'               => $p->id,
            'designation'      => $p->designation,
            'description'      => $p->description,
            // prix_vente = le prix que le client paie (promotion déjà appliquée)
            'prix_vente'       => $p->prixFinal(),
            // prix_barre = l'ancien prix, seulement s'il y a une promotion
            'prix_barre'       => $enPromo ? $p->prix_vente : null,
            'taux_promotion'   => $p->taux_promotion,
            'quantite_stock'   => $p->quantite_stock,
            'color'            => $p->color,
            'taille_dimension' => $p->taille_dimension,
            'image1'           => $p->image1,
        ];
    }
}
