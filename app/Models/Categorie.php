<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function sousCategories(): HasMany
    {
        return $this->hasMany(SousCategorie::class);
    }
}
