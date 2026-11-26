<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * Changement de règle pour le stock :
 *
 *   AVANT : le stock baissait dès qu'une commande était créée (même fantôme ou annulée, jamais restitué).
 *   APRÈS : le stock baisse seulement quand la commande passe à "validée" ou "livrée"
 *           et il est restitué si elle est ensuite annulée (voir app/Models/Commande.php).
 *
 * Cette migration remet donc en stock les quantités des commandes "en attente" et "annulées",
 * qui avaient été retirées sous l'ancienne règle. À lancer UNE SEULE fois (elle ne se relance pas
 * toute seule : Laravel mémorise les migrations déjà exécutées).
 */
return new class extends Migration
{
    public function up(): void
    {
        $quantites = DB::table('commande_produit as cp')
            ->join('commandes as c', 'c.id', '=', 'cp.commande_id')
            ->whereIn('c.statut', ['en_attente', 'annule'])
            ->whereNotNull('cp.produit_id')
            ->selectRaw('cp.produit_id, SUM(cp.quantite) as total')
            ->groupBy('cp.produit_id')
            ->get();

        foreach ($quantites as $ligne) {
            DB::table('produits')
                ->where('id', $ligne->produit_id)
                ->increment('quantite_stock', (int) $ligne->total);
        }
    }

    public function down(): void
    {
        // Volontairement vide : on ne peut pas retrouver l'ancien stock.
    }
};
