<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Commande;
use Carbon\Carbon;

class GraphiqueVentes extends ChartWidget
{
    protected ?string $heading = 'Évolution des Ventes (7 derniers jours)';
    protected int | string | array $columnSpan = 'full';
        protected static ?int $sort = 2; 
    protected function getData(): array
    {
       $jours = [];
        $donneesVentes = [];

        // Boucle pour récupérer les 7 derniers jours de manière dynamique
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $jours[] = $date->translatedFormat('D d M'); // Format ex: "lun. 01 juin"

            // Somme des ventes validées pour ce jour précis
            $totalJour = Commande::whereIn('statut', ['valide', 'livre'])
                ->whereDate('created_at', $date)
                ->sum('montant_total');

            $donneesVentes[] = $totalJour;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenus (FCFA)',
                    'data' => $donneesVentes,
                    // Personnalisation de la couleur de la courbe (Vert émeraude)
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3, // Arrondit la courbe pour un effet moderne
                ],
            ],
            'labels' => $jours,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
