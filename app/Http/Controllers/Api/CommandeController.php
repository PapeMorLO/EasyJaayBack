<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Entreprise;
use App\Models\LigneCommande;
use App\Models\Produit;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Deux façons de commander depuis la boutique :
 *
 *   store()  : le client remplit son panier + le petit formulaire (nom, téléphone, adresse)
 *   direct() : le client clique sur "Acheter direct sur WhatsApp" pour UN produit
 *
 * Dans les deux cas la commande est ENREGISTRÉE EN BASE avant l'ouverture de WhatsApp,
 * donc le vendeur la voit dans son admin même si le client n'envoie jamais le message.
 *
 * Règle d'or : on ne fait JAMAIS confiance aux prix envoyés par le navigateur.
 * Le client envoie seulement "quel produit, quelle quantité, quelle taille/couleur" ;
 * les prix viennent de la base de données.
 */
class CommandeController extends Controller
{
    // ---------------------------------------------------------------
    // Commande classique (panier + formulaire)
    // ---------------------------------------------------------------
    public function store(Request $request)
    {
        $data = $request->validate([
            'entreprise_slug'           => 'required|string|max:255',
            'nom_client'                => 'required|string|max:255',
            'phone_client'              => 'required|string|max:50',
            'adresse_livraison'         => 'required|string|max:500',
            'canal'                     => 'nullable|string|max:30',
            'produits'                  => 'required|array|min:1|max:30',
            'produits.*.id'             => 'required|integer',
            'produits.*.quantite'       => 'required|integer|min:1|max:50',
            'produits.*.selected_size'  => 'nullable|string|max:50',
            'produits.*.selected_color' => 'nullable|string|max:50',
        ]);

        $shop = $this->trouverBoutique($data['entreprise_slug']);

        $commande = $this->enregistrer(
            $shop,
            [
                'nom_client'        => $data['nom_client'],
                'phone_client'      => $data['phone_client'],
                'adresse_livraison' => $data['adresse_livraison'],
            ],
            $data['produits'],
            'web',
            $data['canal'] ?? null
        );

        return response()->json([
            'status'      => 'success',
            'commande_id' => $commande->id,
            'numero'      => $commande->id,
            'total'       => $commande->montant_total,
        ], 201);
    }

    // ---------------------------------------------------------------
    // Commande directe : un seul produit, sans formulaire
    // ---------------------------------------------------------------
    public function direct(Request $request)
    {
        $data = $request->validate([
            'entreprise_slug'           => 'required|string|max:255',
            'canal'                     => 'nullable|string|max:30',
            'produits'                  => 'required|array|size:1',
            'produits.*.id'             => 'required|integer',
            'produits.*.quantite'       => 'required|integer|min:1|max:50',
            'produits.*.selected_size'  => 'nullable|string|max:50',
            'produits.*.selected_color' => 'nullable|string|max:50',
        ]);

        $shop = $this->trouverBoutique($data['entreprise_slug']);

        // Le client n'a rempli aucun formulaire : on le dit clairement au vendeur
        $commande = $this->enregistrer(
            $shop,
            [
                'nom_client'        => 'Client WhatsApp (direct)',
                'phone_client'      => 'N/A',
                'adresse_livraison' => 'À préciser sur WhatsApp',
            ],
            $data['produits'],
            'direct',
            $data['canal'] ?? null
        );

        return response()->json([
            'status'      => 'success',
            'commande_id' => $commande->id,
            'numero'      => $commande->id,
            'total'       => $commande->montant_total,
        ], 201);
    }

    // ---------------------------------------------------------------
    // Le cœur : vérifier, calculer les prix, enregistrer
    // ---------------------------------------------------------------
    private function trouverBoutique(string $slug): Entreprise
    {
        $shop = Entreprise::where('slug', $slug)->where('is_active', true)->first();

        if (! $shop) {
            abort(404, 'Boutique introuvable.');
        }

        return $shop;
    }

    private function enregistrer(Entreprise $shop, array $client, array $lignes, string $source, ?string $canal): Commande
    {
        $canal = $this->nettoyerCanal($canal);

        try {
            // Transaction : si UNE ligne pose problème, rien n'est enregistré
            $commande = DB::transaction(function () use ($shop, $client, $lignes, $source, $canal) {
                $total = 0;
                $detail = [];

                foreach ($lignes as $ligne) {
                    // 1. Le produit doit appartenir à CETTE boutique et être publié
                    $produit = Produit::where('entreprise_id', $shop->id)
                        ->where('etat', 'publie')
                        ->find($ligne['id']);

                    if (! $produit) {
                        abort(422, "Un article n'est plus disponible dans cette boutique.");
                    }

                    // 2. Stock suffisant ? (le stock ne baisse qu'à la validation, voir Commande.php)
                    if ($produit->quantite_stock < $ligne['quantite']) {
                        abort(422, "« {$produit->designation} » : il n'en reste que {$produit->quantite_stock}.");
                    }

                    // 3. Taille / couleur : obligatoires si le produit en propose, et doivent exister
                    $taille  = $this->optionChoisie($produit, 'taille_dimension', $ligne['selected_size'] ?? null, 'une taille');
                    $couleur = $this->optionChoisie($produit, 'color', $ligne['selected_color'] ?? null, 'une couleur');

                    // 4. Le prix vient de la base, jamais du navigateur
                    $prix = $produit->prixFinal();
                    $total += $prix * $ligne['quantite'];

                    $detail[] = [
                        'produit_id'    => $produit->id,
                        'designation'   => $produit->designation,
                        'quantite'      => $ligne['quantite'],
                        'prix_unitaire' => $prix,
                        'taille'        => $taille,
                        'couleur'       => $couleur,
                    ];
                }

                $commande = Commande::create($client + [
                    'entreprise_id' => $shop->id,
                    'montant_total' => $total,
                    'statut'        => 'en_attente',
                    'source'        => $source,
                    'canal'         => $canal,
                ]);

                foreach ($detail as $ligne) {
                    LigneCommande::create($ligne + ['commande_id' => $commande->id]);
                }

                return $commande;
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            throw $e;   // nos abort(422, ...) : on laisse Laravel renvoyer le message au client
        } catch (\Throwable $e) {
            // Bug inattendu : on le note dans les logs, on ne montre PAS les détails au client
            Log::error('Commande impossible : ' . $e->getMessage(), ['boutique' => $shop->slug]);
            abort(500, "Impossible d'enregistrer la commande pour le moment.");
        }

        $this->prevenirLeVendeur($shop, $commande);

        return $commande;
    }

    // Vérifie la taille ou la couleur choisie. Renvoie la valeur à enregistrer (ou null).
    private function optionChoisie(Produit $produit, string $colonne, ?string $choix, string $libelle): ?string
    {
        $disponibles = array_map('strval', $produit->{$colonne} ?? []);

        if (count($disponibles) === 0) {
            return null;   // produit sans variantes
        }

        if ($choix === null || ! in_array($choix, $disponibles, true)) {
            abort(422, "Choisissez {$libelle} pour « {$produit->designation} ».");
        }

        return $choix;
    }

    // Garde seulement lettres, chiffres, tiret : "Facebook" -> "facebook"
    private function nettoyerCanal(?string $canal): ?string
    {
        $canal = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $canal));

        return $canal !== '' ? $canal : null;
    }

    // Une notification (cloche) dans l'admin du vendeur. Si ça échoue, la commande reste valide.
    private function prevenirLeVendeur(Entreprise $shop, Commande $commande): void
    {
        try {
            $titre = $commande->source === 'direct'
                ? "Un client veut commander sur WhatsApp (n°{$commande->id})"
                : "Nouvelle commande n°{$commande->id}";

            Notification::make()
                ->title($titre)
                ->body(number_format($commande->montant_total, 0, ',', ' ') . ' FCFA')
                ->icon('heroicon-o-shopping-cart')
                ->sendToDatabase($shop->users);
        } catch (\Throwable $e) {
            Log::warning('Notification de commande non envoyée : ' . $e->getMessage());
        }
    }
}
