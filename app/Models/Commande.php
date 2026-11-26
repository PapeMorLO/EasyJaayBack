<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Commande extends Model
{
    // Une commande "compte" dans le stock seulement quand elle est dans l'un de ces statuts.
    public const STATUTS_QUI_PRENNENT_LE_STOCK = ['valide', 'livre'];

    protected $fillable = [
        'entreprise_id',
        'nom_client',
        'phone_client',
        'adresse_livraison',
        'montant_total',
        'statut',
        'source',   // web (panier) | direct (bouton WhatsApp) | pos (caisse)
        'canal',    // facebook, snapchat, ... (d'où vient le visiteur)
    ];

    /**
     * Gestion du stock quand le vendeur change le statut d'une commande
     * (par exemple avec la liste déroulante dans la table des commandes) :
     *
     *   en attente -> validée / livrée  : le stock BAISSE
     *   validée / livrée -> annulée     : le stock est RESTITUÉ
     *
     * Les ventes de la caisse (POS) créent la commande directement en "livrée" et gèrent
     * leur stock elles-mêmes (voir POSCaisse.php), donc ce code ne s'en occupe pas.
     */
    protected static function booted(): void
    {
        static::updated(function (Commande $commande) {
            if (! $commande->wasChanged('statut')) {
                return;
            }

            $avant = in_array($commande->getOriginal('statut'), self::STATUTS_QUI_PRENNENT_LE_STOCK, true);
            $apres = in_array($commande->statut, self::STATUTS_QUI_PRENNENT_LE_STOCK, true);

            if ($avant === $apres) {
                return; // pas de changement côté stock (ex : en attente -> annulée)
            }

            DB::transaction(function () use ($commande, $apres) {
                foreach ($commande->lignes as $ligne) {
                    if (! $ligne->produit_id) {
                        continue; // produit supprimé depuis : rien à ajuster
                    }

                    $produit = Produit::lockForUpdate()->find($ligne->produit_id);
                    if (! $produit) {
                        continue;
                    }

                    $nouveauStock = $apres
                        ? $produit->quantite_stock - $ligne->quantite   // la commande est validée
                        : $produit->quantite_stock + $ligne->quantite;  // la commande est annulée

                    $produit->update(['quantite_stock' => max(0, $nouveauStock)]);
                }
            });
        });
    }

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    // Les lignes de la commande (à utiliser en priorité : elles gardent le nom, la taille, la couleur)
    public function lignes(): HasMany
    {
        return $this->hasMany(LigneCommande::class);
    }

    /**
     * Ancienne relation, gardée pour que le reçu de la caisse continue de fonctionner.
     */
    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class)
                    ->withPivot('quantite', 'prix_unitaire', 'designation', 'taille', 'couleur')
                    ->withTimestamps();
    }
}
