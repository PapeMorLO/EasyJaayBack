<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Une ligne d'une commande = une ligne de la table commande_produit.
 *
 * Pourquoi un modèle à part ? Avec la relation "belongsToMany" les lignes dont le produit
 * a été supprimé disparaissent. Ici on garde toujours la ligne, avec le nom copié au moment de l'achat.
 */
class LigneCommande extends Model
{
    protected $table = 'commande_produit';

    protected $fillable = [
        'commande_id', 'produit_id', 'designation',
        'quantite', 'prix_unitaire', 'taille', 'couleur',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    // Nom à afficher : celui copié à l'achat, sinon celui du produit, sinon "Produit supprimé"
    protected function nom(): Attribute
    {
        return Attribute::get(
            fn () => $this->designation ?? $this->produit?->designation ?? 'Produit supprimé'
        );
    }
}
