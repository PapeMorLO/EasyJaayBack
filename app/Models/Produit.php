<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produit extends Model
{
    //
    protected $fillable = [
        'entreprise_id', 'sous_categorie_id', 'reference', 'designation', 
        'description', 'type', 'prix_achat', 'prix_vente', 'taux_promotion', 
        'quantite_stock', 'color', 'taille_dimension', 
        'image1', 'image2', 'image3', 'image4', 'etat'
    ];
    protected $casts = [
        'image1' => 'array', // <-- TRÈS IMPORTANT pour l'upload multiple !
        'prix_vente' => 'integer',
        'quantite_stock' => 'integer',
    ];
    
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function sousCategorie(): BelongsTo
    {
        return $this->belongsTo(SousCategorie::class, 'sous_categorie_id');
    }

    public function commandes(): BelongsToMany
    {
        return $this->belongsToMany(Commande::class)->withPivot('quantite', 'prix_unitaire');
    }
}
