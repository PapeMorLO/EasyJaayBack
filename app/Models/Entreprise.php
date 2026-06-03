<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
//use Filament\Models\Contracts\HasCurrentTenantLabel;
use Filament\Models\Contracts\HasName;

class Entreprise extends Model implements HasName
{
    //
    protected $fillable = [
        'raison_sociale', 'slug', 'logo', 'couleur_theme', 
        'adresse', 'contact_call', 'contact_whatsapp', 'site_web', 'is_active'
    ];

    // Une entreprise a plusieurs utilisateurs/agents
    public function users(): HasMany
    { 
        return $this->hasMany(User::class);
    }
 
    // Une entreprise a ses propres catégories
    public function categories(): HasMany
    {
        return $this->hasMany(Categorie::class);
    }

    // Une entreprise a ses propres produits
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }

    // Une entreprise reçoit plusieurs commandes
    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }

        /**
     * Indique à Laravel de chercher par le slug dans les routes impliquant ce modèle.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

        // Affiche le nom de la PME dans le sélecteur Filament
    public function getFilamentName(): string
    {
        return $this->raison_sociale ?? 'PME Locale';
    }
}
