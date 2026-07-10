<?php

namespace App\Filament\Superadmin\Resources\Utilisateurs\Tables;

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
                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'danger',
                        'admin' => 'success',
                        'vendeur' => 'info',
                        default => 'slate',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'superadmin' => 'Super Admin',
                        'admin' => 'Propriétaire',
                        'vendeur' => 'Vendeur / POS',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('entreprise.raison_sociale')
                    ->label('Boutique')
                    ->placeholder('Aucune (Global)')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filtrer par Rôle')
                    ->options([
                        'superadmin' => 'Super Admin',
                        'admin' => 'Propriétaire (Admin)',
                        'vendeur' => 'Vendeur / POS',
                    ])
                    ->native(false),
                    
                Tables\Filters\SelectFilter::make('entreprise_id')
                    ->label('Filtrer par Boutique')
                    ->relationship('entreprise', 'raison_sociale')
                    ->searchable()
                    ->preload()
                    ->native(false),
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
