<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Commande;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

/**
 * "Où s'arrêtent mes visiteurs ?"  (30 derniers jours, pour la boutique connectée)
 *
 *   Visites  ->  Produits vus  ->  Ajouts au panier  ->  Commandes
 *
 * Les chiffres viennent de la table "evenements" (alimentée par la boutique Next.js)
 * et de la table "commandes".
 */
class EntonnoirBoutique extends StatsOverviewWidget
{
    protected ?string $heading = 'Parcours des visiteurs (30 derniers jours)';

    // Affiché après StatsBoutique (1) et GraphiqueVentes (2)
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $entrepriseId = Filament::getTenant()->id;
        $depuis = now()->subDays(30);

        $compter = fn (string $type) => DB::table('evenements')
            ->where('entreprise_id', $entrepriseId)
            ->where('type', $type)
            ->where('created_at', '>=', $depuis)
            ->count();

        $visites = $compter('visite_boutique');
        $produitsVus = $compter('vue_produit');
        $paniers = $compter('ajout_panier');

        // On ne compte pas les ventes de la caisse : ce n'est pas du trafic en ligne
        $commandes = Commande::where('entreprise_id', $entrepriseId)
            ->where('source', '!=', 'pos')
            ->where('created_at', '>=', $depuis)
            ->count();

        $pourcentage = fn (int $partie, int $total) => $total > 0
            ? round($partie / $total * 100) . ' % des visites'
            : 'Pas encore de données';

        return [
            Stat::make('Visiteurs (30 jours)', $visites),
            Stat::make('Produits consultés', $produitsVus)
                ->description($pourcentage($produitsVus, $visites)),
            Stat::make('Ajouts au panier', $paniers)
                ->description($pourcentage($paniers, $visites)),
            Stat::make('Commandes reçues', $commandes)
                ->description($pourcentage($commandes, $visites)),
        ];
    }
}
