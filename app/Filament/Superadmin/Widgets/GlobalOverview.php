<?php

namespace App\Filament\Superadmin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Entreprise;
use App\Models\User;
use App\Models\Commande; 
//use App\Models\Vente;

class GlobalOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            //
           // 1. Vos statistiques existantes optimisées
            Stat::make('Boutiques Actives', Entreprise::where('is_active', true)->count())
                ->description('Total des locataires sur la plateforme')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),
                
            Stat::make('Utilisateurs Inscrits', User::count())
                ->description('Marchands et administrateurs')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            // 2. NOUVEAU : Alerte sur les boutiques inactives / suspendues (Abonnements coupés)
            Stat::make('Boutiques Suspendues', Entreprise::where('is_active', false)->count())
                ->description('Accès coupés ou abonnements expirés')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color(Entreprise::where('is_active', false)->count() > 0 ? 'danger' : 'gray'),

            // 3. NOUVEAU : Volume d'Affaires Global (GMV) basé sur votre table 'commandes'
            Stat::make('Volume de vente', number_format(Commande::where('statut', '!=', 'annule')->sum('montant_total'), 0, ',', ' ') . ' FCFA')
                ->description('Chiffre d\'affaires global généré par les boutiques')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        
        ];
    }
}
