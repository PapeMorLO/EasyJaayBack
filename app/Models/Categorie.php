<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categorie extends Model
{
    //
    protected $fillable = [
        'entreprise_id',
        'designation',
        'description',
        'image_categorie',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'parent_id');
    }

    /**
     * Obtenir les sous-catégories directes
     */
    public function enfants(): HasMany
    {
        return $this->hasMany(Categorie::class, 'parent_id');
    }

    /**
     * Les produits associés à cette catégorie
     */
    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class, 'categorie_produit');
    }
}
