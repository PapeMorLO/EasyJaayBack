<?php

namespace App\Filament\Admin\Resources\Produits\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Forms\Form;

class ProduitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Forms\Components\TextInput::make('designation')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('prix_vente')
                    ->numeric()
                    ->required()
                    ->suffix('FCFA'),
                Forms\Components\TextInput::make('quantite_stock')
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\Select::make('sous_categorie_id')
                    ->relationship('sousCategorie', 'designation')
                    ->required()
                    ->searchable(),
                Forms\Components\FileUpload::make('image1')
                     ->label('Photos du produit (Galerie)')
                    ->image()
                    ->multiple() // <-- Permet de téléverser plusieurs images à la fois !
                    ->reorderable() // Glisser-déposer pour réorganiser l'ordre d'affichage
                    ->appendFiles() // Permet d'ajouter de nouvelles photos sans écraser les anciennes
                    ->directory('produits')
                    ->disk('public') // Utilise le stockage public
                    ->visibility('public')
                    ->helperText('Vous pouvez glisser-déposer pour réorganiser les images. Maintenez Ctrl / Cmd pour en sélectionner plusieurs.')
                    ->columnSpanFull(), // Prend toute la largeur pour une meilleure visibilité de la galerie
            ]);
    }
}
