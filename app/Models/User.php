<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
 * IMPORTANT : la classe implémente maintenant FilamentUser.
 * Sans cela, Filament n'appelle jamais canAccessPanel() et, hors environnement "local",
 * il refuse l'accès à l'admin.
 */
class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
       'entreprise_id', 'name', 'prenom', 'email', 'phone', 'password', 'role', 'adresse'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Filament : les boutiques accessibles par l'utilisateur
     */
    public function getTenants(Panel $panel): Collection
    {
        return collect([$this->entreprise])->filter();
    }

    /**
     * Filament : sécurité d'accès à une boutique
     */
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->entreprise_id === $tenant->id;
    }

    public function getTenantName(Model $tenant): string
    {
        return $tenant instanceof Entreprise ? (string) $tenant->raison_sociale : 'PME Locale';
    }

    /**
     * Qui a le droit d'entrer dans l'admin ? Ton seul panel s'appelle "admin" :
     * tout utilisateur rattaché à une boutique peut y entrer.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->entreprise_id !== null;
    }
}
