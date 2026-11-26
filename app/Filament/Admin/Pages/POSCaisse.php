<?php

namespace App\Filament\Admin\Pages;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Produit;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use BackedEnum;

class POSCaisse extends Page
{
    protected string $view = 'filament.admin.pages.p-o-s-caisse';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationLabel = 'Caisse (POS)';
    protected static ?string $title = 'Terminal de Caisse';

    // Placer la caisse en haut du menu
    protected static ?int $navigationSort = 1;

    // États du composant
    public $search = '';
    public $panier = [];
    public $nomClient = 'Client Comptoir';
    public $phoneClient = '';

    // ID de la dernière vente (pour imprimer le reçu)
    public $lastCommandeId = null;

    // La boutique de l'utilisateur connecté
    private function entrepriseId(): int
    {
        return (int) auth()->user()->entreprise_id;
    }

    // Retrouve un produit, mais SEULEMENT s'il appartient à la boutique connectée et est publié.
    // (Avant : Produit::find($id) acceptait n'importe quel produit de n'importe quelle boutique.)
    private function produitDeMaBoutique($produitId): ?Produit
    {
        return Produit::where('entreprise_id', $this->entrepriseId())
            ->where('etat', 'publie')
            ->find($produitId);
    }

    // Filtrer les produits en temps réel
    public function getProduitsProperty()
    {
        return Produit::where('entreprise_id', $this->entrepriseId())
            ->where('etat', 'publie')
            ->when($this->search, function ($query) {
                // Les parenthèses sont importantes : sans elles, le "OR" ignorait le filtre de boutique
                // et la recherche affichait les produits des autres boutiques.
                $query->where(function ($q) {
                    $q->where('designation', 'like', '%' . $this->search . '%')
                      ->orWhere('reference', 'like', '%' . $this->search . '%');
                });
            })
            ->get();
    }

    public function ajouterAuPanier($produitId)
    {
        $produit = $this->produitDeMaBoutique($produitId);

        if (!$produit || $produit->quantite_stock <= 0) {
            Notification::make()->danger()->title('Produit en rupture de stock !')->send();
            return;
        }

        if (isset($this->panier[$produitId])) {
            if ($this->panier[$produitId]['quantite'] >= $produit->quantite_stock) {
                Notification::make()->warning()->title('Stock maximum atteint')->send();
                return;
            }
            $this->panier[$produitId]['quantite']++;
        } else {
            $this->panier[$produitId] = [
                'id' => $produit->id,
                'designation' => $produit->designation,
                'prix' => $produit->prixFinal(),
                'quantite' => 1,
            ];
        }
    }

    public function incrementerQuantite($produitId)
    {
        if (!isset($this->panier[$produitId])) {
            return;
        }

        $produit = $this->produitDeMaBoutique($produitId);

        if ($produit && $this->panier[$produitId]['quantite'] < $produit->quantite_stock) {
            $this->panier[$produitId]['quantite']++;
        } else {
            Notification::make()->warning()->title('Stock insuffisant')->send();
        }
    }

    public function decrementerQuantite($produitId)
    {
        if (!isset($this->panier[$produitId])) {
            return;
        }

        if ($this->panier[$produitId]['quantite'] > 1) {
            $this->panier[$produitId]['quantite']--;
        } else {
            unset($this->panier[$produitId]);
        }
    }

    public function retirerDuPanier($produitId)
    {
        unset($this->panier[$produitId]);
    }

    // Total affiché à l'écran (le vrai total est recalculé à l'encaissement)
    public function getTotalProperty()
    {
        return collect($this->panier)->sum(function ($item) {
            return $item['prix'] * $item['quantite'];
        });
    }

    public function encaisser()
    {
        if (empty($this->panier)) {
            Notification::make()->danger()->title('Le panier est vide.')->send();
            return;
        }

        try {
            $commande = DB::transaction(function () {
                $total = 0;
                $detail = [];

                // Le panier vit dans le navigateur (propriété publique Livewire) : on ne lui fait pas
                // confiance. Pour chaque ligne on relit le produit, le stock et le prix en base.
                foreach ($this->panier as $item) {
                    $quantite = (int) ($item['quantite'] ?? 0);

                    $produit = Produit::where('entreprise_id', $this->entrepriseId())
                        ->where('etat', 'publie')
                        ->lockForUpdate()                    // évite deux ventes simultanées sur le même stock
                        ->find($item['id'] ?? 0);

                    if (!$produit || $quantite < 1) {
                        throw new \RuntimeException('Un produit du panier est invalide.');
                    }

                    if ($produit->quantite_stock < $quantite) {
                        throw new \RuntimeException("« {$produit->designation} » : il n'en reste que {$produit->quantite_stock}.");
                    }

                    $prix = $produit->prixFinal();
                    $total += $prix * $quantite;

                    $detail[] = [$produit, $quantite, $prix];
                }

                $commande = Commande::create([
                    'entreprise_id' => $this->entrepriseId(),
                    'nom_client' => $this->nomClient ?: 'Client Comptoir',
                    'phone_client' => $this->phoneClient ?: 'N/A',
                    'adresse_livraison' => 'Vente au comptoir (POS)',
                    'montant_total' => $total,
                    'statut' => 'livre',
                    'source' => 'pos',
                ]);

                foreach ($detail as [$produit, $quantite, $prix]) {
                    LigneCommande::create([
                        'commande_id' => $commande->id,
                        'produit_id' => $produit->id,
                        'designation' => $produit->designation,
                        'quantite' => $quantite,
                        'prix_unitaire' => $prix,
                    ]);

                    // La caisse vend tout de suite : le stock baisse maintenant
                    $produit->decrement('quantite_stock', $quantite);
                }

                return $commande;
            });
        } catch (\RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
            return;
        }

        // On sauvegarde l'ID pour l'interface (reçu)
        $this->lastCommandeId = $commande->id;

        // On vide le panier
        $this->panier = [];

        Notification::make()->success()->title('Vente validée !')->send();
    }

    // Remet la caisse à zéro après l'impression du reçu
    public function nouvelleVente()
    {
        $this->reset(['search', 'phoneClient', 'lastCommandeId']);
        $this->nomClient = 'Client Comptoir';
    }

}
