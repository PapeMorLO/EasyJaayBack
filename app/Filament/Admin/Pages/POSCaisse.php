<?php

namespace App\Filament\Admin\Pages;

use App\Models\Produit;
use App\Models\Commande;
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

    // Ajoutez cette variable en haut de votre classe
    public $lastCommandeId = null;

    // Filtrer les produits en temps réel
    public function getProduitsProperty()
    {
        return Produit::where('entreprise_id', auth()->user()->entreprise_id)
            ->where('etat', 'publie')
            ->when($this->search, function ($query) {
                $query->where('designation', 'like', '%' . $this->search . '%')
                      ->orWhere('reference', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function ajouterAuPanier($produitId)
    {
        $produit = Produit::find($produitId);

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
                'prix' => $produit->prix_vente,
                'quantite' => 1,
            ];
        }
    }

    public function incrementerQuantite($produitId)
    {
        $produit = Produit::find($produitId);
        if ($this->panier[$produitId]['quantite'] < $produit->quantite_stock) {
            $this->panier[$produitId]['quantite']++;
        } else {
            Notification::make()->warning()->title('Stock insuffisant')->send();
        }
    }

    public function decrementerQuantite($produitId)
    {
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

        $commandeFinale = null;

        DB::transaction(function () use (&$commandeFinale) {
            $commandeFinale = Commande::create([
                'entreprise_id' => auth()->user()->entreprise_id,
                'nom_client' => $this->nomClient,
                'phone_client' => $this->phoneClient ?: 'N/A',
                'adresse_livraison' => 'Vente au comptoir (POS)',
                'montant_total' => $this->total,
                'statut' => 'livre',
            ]);

            foreach ($this->panier as $item) {
                $commandeFinale->produits()->attach($item['id'], [
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $item['prix'],
                ]);
                Produit::find($item['id'])->decrement('quantite_stock', $item['quantite']);
            }
        });

        // On sauvegarde l'ID pour l'interface
        $this->lastCommandeId = $commandeFinale->id;
        
        // On vide le panier
        $this->panier = [];
        
        Notification::make()->success()->title('Vente validée !')->send();
    }

    
}
