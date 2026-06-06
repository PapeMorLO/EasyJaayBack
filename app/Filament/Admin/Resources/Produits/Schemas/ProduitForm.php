<?php

namespace App\Filament\Admin\Resources\Produits\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Schemas\Components\Section; 
use Filament\Schemas\Components\Grid; 

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

                // Remplacement du Select simple par un Select Multiple lié à la relation 'categories'
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'designation')
                    ->multiple() // Active la sélection multiple (WooCommerce style)
                    ->searchable()
                    ->preload()
                    ->label('Catégories du produit')
                    ->placeholder('Sélectionnez une ou plusieurs catégories...'),

                Section::make('Variations & Options du produit')
                    ->description('Configurez les variantes (Tailles, Pointures, Couleurs, Contenances...) si nécessaire. Laissez vide si le produit n\'a pas de déclinaison.')
                    ->collapsible() // Permet de replier la section pour ne pas encombrer l'écran
                    ->schema([
                        Forms\Components\TagsInput::make('taille_dimension')
                            ->label('Tailles / Pointures disponibles')
                            ->placeholder('Ajouter une taille (ex: M, XL, 42, 43)...'),

                        Forms\Components\TagsInput::make('color')
                            ->label('Couleurs disponibles')
                            ->placeholder('Ajouter une couleur (ex: Noir, Blanc, Rouge)...'),
                    ]),
                    
                Forms\Components\FileUpload::make('image1')
                    ->label('Photos du produit (Galerie)')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->directory('produits')
                    ->disk('public')
                    ->visibility('public')
                    ->helperText('Vous pouvez glisser-déposer pour réorganiser les images. Maintenez Ctrl / Cmd pour en sélectionner plusieurs.')
                    ->columnSpanFull(),
            ]);
    }
}
