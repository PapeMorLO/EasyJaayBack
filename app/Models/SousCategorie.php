<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SousCategorie extends Model
{
    //
    protected $fillable = [
        'entreprise_id',
        'categorie_id',
        'designation',
        'description',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'sous_categorie_id');
    }
}
