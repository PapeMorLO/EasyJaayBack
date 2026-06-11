<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Commande;
use App\Models\Produit;
use Filament\Support\Enums\IconPosition;
use Filament\Facades\Filament;

class StatsBoutique extends BaseWidget
{
    // Rechargement automatique du widget toutes les 15 secondes
    protected ?string $pollingInterval = '15s';
    protected static ?int $sort = 1; 

    protected function getStats(): array
    {
        // Récupération sécurisée du tenant (l'Entreprise) connecté via Filament
        $currentTenant = Filament::getTenant();

        // 1. Calcul du chiffre d'affaires (uniquement les commandes validées ou livrées)
        // Les requêtes sont automatiquement filtrées sur le tenant actuel par Filament
        $revenuTotal = Commande::whereIn('statut', ['valide', 'livre'])->sum('montant_total');

        // 2. Nombre de commandes en attente de traitement
        $commandesEnAttente = Commande::where('statut', 'en_attente')->count();

        // 3. Alerte sur les produits en stock critique (Moins de 5 articles)
        $produitsCritiques = Produit::where('quantite_stock', '<=', 5)->count();

        // 4. Récupération dynamique du nombre de visites de la boutique connectée
        $visites = $currentTenant ? $currentTenant->visites : 0; 

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
            
            Stat::make('Visites de la boutique', number_format($visites, 0, ',', ' '))
                ->description('Total des ouvertures uniques de votre boutique en ligne')
                ->descriptionIcon('heroicon-m-eye', IconPosition::Before)
                ->color('info')
        ];
    }
}