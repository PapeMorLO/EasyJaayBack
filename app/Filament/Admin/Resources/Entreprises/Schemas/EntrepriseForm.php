<?php

namespace App\Filament\Admin\Resources\Entreprises\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms; 
use Filament\Schemas\Components\Section; 

class EntrepriseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                  Section::make('Identité visuelle de votre boutique')
                    ->description('Ces éléments personnaliseront directement votre vitrine publique.')
                    ->schema([
                        Forms\Components\TextInput::make('raison_sociale')
                            ->label('Nom de la boutique (Raison sociale)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug de l\'adresse web (Généré au départ)')
                            ->disabled() // Protège l'URL de votre vitrine contre les modifications accidentelles
                            ->dehydrated(false),

                        Forms\Components\ColorPicker::make('couleur_theme')
                            ->label('Couleur du thème global')
                            ->default('#10b981')
                            ->helperText('Cette couleur s\'appliquera sur les boutons, le panier et les badges de votre vitrine.'),

                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo de votre commerce')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->visibility('public'),
                    ])->columns(2),

                Section::make('Canaux de contact et commandes')
                    ->description('Numéros essentiels pour la redirection de vos paniers WhatsApp.')
                    ->schema([
                        Forms\Components\TextInput::make('contact_whatsapp')
                            ->label('Numéro WhatsApp pour recevoir les commandes')
                            ->helperText('Ex: 221771234567 (avec indicatif pays, sans espaces ni caractères)')
                            ->required(),

                        Forms\Components\TextInput::make('contact_call')
                            ->label('Numéro d\'appel client')
                            ->required(),

                        Forms\Components\TextInput::make('adresse')
                            ->label('Adresse physique de votre commerce'),

                        Forms\Components\TextInput::make('site_web')
                            ->label('Lien site web optionnel')
                            ->url(),
                    ])->columns(2)
            ]);
    }
}
