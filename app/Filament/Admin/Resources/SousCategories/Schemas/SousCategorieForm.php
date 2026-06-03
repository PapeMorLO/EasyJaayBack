<?php

namespace App\Filament\Admin\Resources\SousCategories\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section; 
use Filaments\Forms; 
use Filament\Forms\Components\Select; 
use Filament\Forms\Components\TextInput; 
use Filament\Forms\Components\Textarea; 

class SousCategorieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make()
                    ->schema([
                        Select::make('categorie_id')
                            ->relationship('categorie', 'designation') // Relation filtrée par tenant automatiquement
                            ->required()
                            ->searchable()
                            ->label('Catégorie Parente'),
                        TextInput::make('designation')
                            ->required()
                            ->label('Nom de la sous-catégorie'),
                        Textarea::make('description')->columnSpanFull(),
                    ])
            ]);
    }
}
