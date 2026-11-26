<?php

namespace App\Filament\Admin\Resources\Produits\Schemas;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ProduitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('designation')
                    ->label('Nom du produit')
                    ->required()
                    ->maxLength(255),

                // Avant : ce champ n'existait pas, donc toutes les fiches affichaient
                // "Aucune description supplémentaire fournie pour cet article."
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->maxLength(2000)
                    ->helperText("Décrivez le produit : matière, usage, ce qui rassure l'acheteur (qualité, origine, garantie...).")
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('prix_vente')
                    ->label('Prix de vente')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->suffix('FCFA'),

                Forms\Components\TextInput::make('taux_promotion')
                    ->label('Promotion')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(90)
                    ->suffix('%')
                    ->helperText('0 = pas de promotion. Le client verra l\'ancien prix barré.'),

                Forms\Components\TextInput::make('prix_achat')
                    ->label("Prix d'achat (facultatif)")
                    ->numeric()
                    ->minValue(0)
                    ->suffix('FCFA')
                    ->helperText('Visible uniquement par vous. Sert à calculer vos marges.'),

                Forms\Components\TextInput::make('quantite_stock')
                    ->label('Quantité en stock')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0),

                Forms\Components\TextInput::make('reference')
                    ->label('Référence (facultatif)')
                    ->maxLength(100)
                    // Unique dans MA boutique seulement (deux boutiques peuvent avoir le même code)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('entreprise_id', Filament::getTenant()->id)
                    ),

                Forms\Components\Select::make('etat')
                    ->label('Visibilité')
                    ->options([
                        'publie'    => 'Publié (visible sur la boutique)',
                        'brouillon' => 'Brouillon (caché aux clients)',
                    ])
                    ->default('publie')
                    ->required(),

                // Sélection multiple liée à la relation 'categories'
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'designation')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Catégories du produit')
                    ->placeholder('Sélectionnez une ou plusieurs catégories...'),

                Section::make('Variations & Options du produit')
                    ->description("Configurez les variantes (Tailles, Pointures, Couleurs...) si nécessaire. Laissez vide si le produit n'a pas de déclinaison.")
                    ->collapsible()
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
                    ->maxFiles(6)
                    // Les photos de téléphone font 4 à 8 Mo : on les réduit AVANT l'envoi pour que
                    // la boutique charge vite sur mobile. La largeur max est de 1200 px, sans déformer.
                    ->maxSize(10240)
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('1200')
                    ->imageResizeUpscale(false)
                    ->directory('produits')
                    ->disk('public')
                    ->visibility('public')
                    ->helperText('Jusqu\'à 6 photos. Glissez-déposez pour réorganiser : la première est la photo principale.')
                    ->columnSpanFull(),
            ]);
    }
}
