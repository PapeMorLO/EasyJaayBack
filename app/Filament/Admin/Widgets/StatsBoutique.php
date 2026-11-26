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
        $boutique = Filament::getTenant();
        $id = $boutique?->id;

        // Chaque requête filtre explicitement sur la boutique connectée :
        // on ne dépend pas du filtrage automatique de Filament.

        // 1. Chiffre d'affaires (commandes validées, livrées, et ventes de la caisse)
        $revenuTotal = Commande::where('entreprise_id', $id)
            ->whereIn('statut', ['valide', 'livre'])
            ->sum('montant_total');

        // 2. Commandes à traiter
        $commandesEnAttente = Commande::where('entreprise_id', $id)
            ->where('statut', 'en_attente')
            ->count();

        // 3. Stock critique : produits physiques publiés avec 5 articles ou moins
        $produitsCritiques = Produit::where('entreprise_id', $id)
            ->where('etat', 'publie')
            ->where('type', 'physique')
            ->where('quantite_stock', '<=', 5)
            ->count();

        // 4. Visites depuis le début (compteur de l'entreprise)
        $visites = $boutique ? $boutique->visites : 0;

        return [
            Stat::make('Revenus Totaux', number_format($revenuTotal, 0, ',', ' ') . ' FCFA')
                ->description('Chiffre d\'affaires encaissé ou validé')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Commandes en Attente', $commandesEnAttente)
                ->description('Commandes à valider, préparer ou livrer')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color($commandesEnAttente > 0 ? 'warning' : 'gray'),

            Stat::make('Alertes Stock', $produitsCritiques)
                ->description('Produits avec 5 articles ou moins')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($produitsCritiques > 0 ? 'danger' : 'success'),

            Stat::make('Visites depuis le début', number_format($visites, 0, ',', ' '))
                ->description('Ouvertures uniques de votre boutique en ligne')
                ->descriptionIcon('heroicon-m-eye', IconPosition::Before)
                ->color('info'),
        ];
    }
}
