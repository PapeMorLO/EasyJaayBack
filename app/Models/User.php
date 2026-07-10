<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class User extends Authenticatable implements HasTenants
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
     * Filament v5 : Récupère la collection d'entreprises accessibles par l'utilisateur
     */
    public function getTenants(Panel $panel): Collection
    {
        return collect([$this->entreprise])->filter();
    }

    /**
     * Filament v5 : Sécurité d'accès au tenant
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
     * Contrôle d'accès dynamique aux différents panels de Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Si on tente d'accéder au panel "superadmin"
        if ($panel->getId() === 'superadmin') {
            // Seuls les utilisateurs avec un rôle spécifique ou votre adresse email peuvent y entrer
            return $this->role === 'superadmin' || $this->email === 'votre-email@djolofxarala.com';
        }

        // Si on tente d'accéder au panel "merchant" (la boutique)
        if ($panel->getId() === 'merchant') {
            // Accès autorisé pour les gérants de boutique
            return $this->entreprise_id !== null;
        }

        return false;
    }

}
