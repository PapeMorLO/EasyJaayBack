<?php

namespace App\Filament\Admin\Resources\Utilisateurs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables; 

class UtilisateursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('prenom')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable()->label('Nom'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone')->label('Téléphone'),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'primary' => 'admin',
                        'warning' => 'vendeur',
                    ]),
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
