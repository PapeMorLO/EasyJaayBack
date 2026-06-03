<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Commande extends Model
{
    //
    protected $fillable = [
        'entreprise_id',
        'nom_client',
        'phone_client',
        'adresse_livraison',
        'montant_total',
        'statut',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    /**
     * Récupère les produits associés à la commande avec les données de la table pivot
     */
    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class)
                    ->withPivot('quantite', 'prix_unitaire')
                    ->withTimestamps();
    }
}
