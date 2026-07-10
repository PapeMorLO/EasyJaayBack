<?php

namespace App\Filament\Superadmin\Resources\Utilisateurs\Schemas;

use Filament\Schemas\Schema;
use App\Models\User; 
use Filament\Forms; 
use Filament\Schemas\Components\Section;

class UtilisateurForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Identité & Rôle')
                    ->description('Gérez les informations personnelles de l\'utilisateur et ses privilèges d\'accès.')
                    ->schema([
                        Forms\Components\TextInput::make('prenom')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de famille')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('role')
                            ->label('Niveau d\'accès (Rôle)')
                            ->required()
                            ->options([
                                'superadmin' => 'Super Admin (Djolof Xarala)',
                                'admin' => 'Administrateur Boutique (Client)',
                                'vendeur' => 'Vendeur / Gérant de Caisse',
                            ])
                            ->native(false),

                        Forms\Components\Select::make('entreprise_id')
                            ->label('Rattaché à la Boutique')
                            ->relationship('entreprise', 'raison_sociale')
                            ->searchable()
                            ->preload()
                            ->helperText('Laissez vide si l\'utilisateur est un Super Admin global.')
                            ->nullable()
                            ->native(false),
                    ])->columns(2),

                Section::make('Coordonnées & Sécurité')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true),
                            
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'phone', ignoreRecord: true),
                            
                        Forms\Components\TextInput::make('adresse')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->helperText(fn (string $context): string => $context === 'edit' ? 'Laissez vide pour ne pas modifier le mot de passe actuel.' : '')
                            ->mutateDehydratedStateUsing(fn ($state) => Hash::make($state)),
                    ])->columns(2),
            ]);
    }
}
