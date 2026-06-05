<?php

namespace App\Filament\Admin\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables; 
use Filament\Tables\Filters\SelectFilter;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('image_categorie')
                    ->label('Image'),
                
                Tables\Columns\TextColumn::make('designation')
                    ->searchable()
                    ->sortable()
                    ->label('Nom'),

                // Nouvelle colonne pour afficher la catégorie parente
                Tables\Columns\TextColumn::make('parent.designation')
                    ->label('Catégorie Parente')
                    ->sortable()
                    ->placeholder('Aucune (Principale)')
                    // Un petit badge gris pour styliser s'il y a un parent
                    ->badge()
                    ->color('gray')
                    ->state(function ($record) {
                        return $record->parent ? $record->parent->designation : null;
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtre pratique pour afficher uniquement les catégories principales ou les sous-catégories
                SelectFilter::make('parent_id')
                    ->label('Type de catégorie')
                    ->options([
                        'principales' => 'Catégories principales',
                        'sous_categories' => 'Sous-catégories',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'principales') {
                            return $query->whereNull('parent_id');
                        }
                        if ($data['value'] === 'sous_categories') {
                            return $query->whereNotNull('parent_id');
                        }
                        return $query;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
