<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produit extends Model
{
    protected $fillable = [
        'entreprise_id', 'parent_id', 'reference', 'designation',
        'description', 'type', 'prix_achat', 'prix_vente', 'taux_promotion',
        'quantite_stock', 'color', 'taille_dimension',
        'image1', 'image2', 'image3', 'image4', 'etat'
    ];

    protected $casts = [
        'image1' => 'array', // la galerie : plusieurs photos dans une seule colonne
        'prix_achat' => 'integer',
        'prix_vente' => 'integer',
        'taux_promotion' => 'integer',
        'quantite_stock' => 'integer',
        'color' => 'array',
        'taille_dimension' => 'array',
    ];

    // Ne jamais envoyer le prix d'achat dans une réponse JSON (API publique de la boutique).
    // Filament continue de le lire normalement.
    protected $hidden = ['prix_achat'];

    /**
     * Le prix que le client paie vraiment (prix de vente moins la promotion).
     * C'est la SEULE fonction qui calcule ce prix : l'API de commande, la caisse
     * et l'API de la boutique l'utilisent toutes, donc elles ne peuvent pas se contredire.
     */
    public function prixFinal(): int
    {
        $taux = max(0, min(100, (int) $this->taux_promotion));

        return intdiv((int) $this->prix_vente * (100 - $taux), 100);
    }

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Categorie::class, 'categorie_produit')
                    ->withTimestamps();
    }

    public function commandes(): BelongsToMany
    {
        return $this->belongsToMany(Commande::class)->withPivot('quantite', 'prix_unitaire');
    }
}
