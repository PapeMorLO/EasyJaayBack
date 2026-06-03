<?php

namespace App\Filament\Admin\Resources\Entreprises\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class EntreprisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('raison_sociale')
                    ->label('Boutique')
                    ->fontFamily('sans')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->badge()
                    ->prefix('/'),
                Tables\Columns\ColorColumn::make('couleur_theme')
                    ->label('Couleur active'),
                Tables\Columns\TextColumn::make('contact_whatsapp')
                    ->label('WhatsApp principal'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
