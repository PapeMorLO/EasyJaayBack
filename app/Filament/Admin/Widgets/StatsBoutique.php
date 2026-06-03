<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Commande;
use App\Models\Produit;

class StatsBoutique extends BaseWidget
{
    // Optionnel : Permet de recharger automatiquement le widget toutes les 15 secondes
    protected ?string $pollingInterval = '15s';
    protected static ?int $sort = 1; 

    protected function getStats(): array
    {
        // Grâce au multi-tenant de Filament v5, ces requêtes pointent UNIQUEMENT
        // sur l'entreprise du commerçant connecté.
        
        // 1. Calcul du chiffre d'affaires (uniquement les commandes validées ou livrées)
        $revenuTotal = Commande::whereIn('statut', ['valide', 'livre'])->sum('montant_total');

        // 2. Nombre de commandes qui attendent un traitement ou un livreur
        $commandesEnAttente = Commande::where('statut', 'en_attente')->count();

        // 3. Alerte sur les produits bientôt en rupture de stock (Moins de 5 articles)
        $produitsCritiques = Produit::where('quantite_stock', '<=', 5)->count();

        return [
            Stat::make('Revenus Totaux', number_format($revenuTotal, 0, ',', ' ') . ' FCFA')
                ->description('Chiffre d\'affaires encaissé ou validé')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Commandes en Attente', $commandesEnAttente)
                ->description('Commandes à préparer ou livrer')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color($commandesEnAttente > 0 ? 'warning' : 'gray'),

            Stat::make('Alertes Stock', $produitsCritiques)
                ->description('Produits avec 5 articles ou moins')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($produitsCritiques > 0 ? 'danger' : 'success'),
        ];
    }
}
