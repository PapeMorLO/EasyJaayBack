<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use App\Models\Categorie;
use Filament\Forms;
//use Filament\Forms\Form;
use Filament\Schemas\Components\Section; 

class CategorieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
               Section::make('Informations de la catégorie')
                    ->schema([
                        Forms\Components\TextInput::make('designation')
                            ->required()
                            ->maxLength(255)
                            ->label('Nom de la catégorie'),

                        // Ajout du champ pour gérer la catégorie parente
                        Forms\Components\Select::make('parent_id')
                            ->relationship(
                                name: 'parent', 
                                titleAttribute: 'designation',
                                // Cette fonction filtre la liste uniquement si la catégorie existe déjà (en mode édition)
                                modifyQueryUsing: fn ($query, ?Categorie $record) => $record 
                                    ? $query->where('id', '!=', $record->id) 
                                    : $query
                            )
                            ->searchable()
                            ->preload()
                            ->label('Catégorie parente')
                            ->placeholder('Aucune (Catégorie principale)'),
                            
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image_categorie')
                            ->image()
                            ->directory('categories')
                            ->label('Image de couverture'),
                ])
            ]);
    }
}
