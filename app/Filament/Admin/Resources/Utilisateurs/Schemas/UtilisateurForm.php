<?php

namespace App\Filament\Admin\Resources\Utilisateurs\Schemas;

use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Filament\Forms; 
use Filament\Schemas\Components\Section;

class UtilisateurForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
               Section::make('Identifiants du collaborateur')
                    ->schema([
                        Forms\Components\TextInput::make('prenom')->required(),
                        Forms\Components\TextInput::make('name')->required()->label('Nom'),
                        Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')->tel()->required()->label('Téléphone'),
                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Administrateur / Gérant',
                                'vendeur' => 'Vendeur / Agent',
                            ])->required(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->label('Mot de passe'),
                    ])->columns(2)
            ]);
    }
}
