<?php

namespace App\Filament\Admin\Resources\SousCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction; 
use Filament\Tables\Table;
use Filament\Tables; 

class SousCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('categorie.designation')->sortable()->label('Catégorie Parente'),
                Tables\Columns\TextColumn::make('designation')->searchable()->label('Sous-catégorie'),
            ])
            ->filters([
                //
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
