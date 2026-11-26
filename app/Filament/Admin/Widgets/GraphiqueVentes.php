<?php

namespace App\Filament\Admin\Widgets;

use Filament\Facades\Filament;
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
        $id = Filament::getTenant()?->id;
        $jours = [];
        $donneesVentes = [];

        // Les 7 derniers jours, du plus ancien à aujourd'hui
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $jours[] = $date->translatedFormat('D d M'); // ex : "lun. 01 juin"

            // Somme des ventes validées / livrées / caisse pour ce jour, de CETTE boutique
            $donneesVentes[] = Commande::where('entreprise_id', $id)
                ->whereIn('statut', ['valide', 'livre'])
                ->whereDate('created_at', $date)
                ->sum('montant_total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenus (FCFA)',
                    'data' => $donneesVentes,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
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
