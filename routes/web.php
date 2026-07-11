<?php

use Illuminate\Support\Facades\Route;
use App\Models\Commande; 

Route::get('/', function () {
    return redirect('/admin/login'); 
});

Route::get('/admin/commandes/{commande}/recu', function (Commande $commande) {
    // Sécurité : vérifier que la commande appartient bien à la boutique connectée
    if ($commande->entreprise_id !== auth()->user()->entreprise_id) {
        abort(403, 'Accès non autorisé');
    }
    
    // Charger la relation pivot pour avoir les quantités et prix
    $commande->load('produits');
    
    return view('filament.admin.pages.recu-pos', compact('commande'));
})->middleware(['auth'])->name('pos.recu');

