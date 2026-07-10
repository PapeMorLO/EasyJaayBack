<?php

namespace App\Filament\Superadmin\Resources\Entreprises\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use App\Models\Entreprise; 

class EntrepriseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Informations Principales')
                    ->description('Gérez l\'identité visuelle et le nom de la boutique du client.')
                    ->schema([
                        Forms\Components\TextInput::make('raison_sociale')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            // Génération auto du slug (optionnel en mode édition, mais pratique)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                            
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Entreprise::class, 'slug', ignoreRecord: true),
                            
                        Forms\Components\ColorPicker::make('couleur_theme')
                            ->label('Couleur du Thème')
                            ->default('#10b981')
                            ->required(),
                            
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('logos')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Contact & Accès')
                    ->schema([
                        Forms\Components\TextInput::make('contact_whatsapp')
                            ->label('Numéro WhatsApp')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('contact_call')
                            ->label('Numéro d\'Appel')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Boutique Active (Abonnement valide)')
                            ->helperText('Si désactivé, le marchand n\'aura plus accès à son panel et sa vitrine sera hors ligne.')
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
