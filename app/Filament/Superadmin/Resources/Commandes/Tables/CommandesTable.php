<?php

namespace App\Filament\Superadmin\Resources\Commandes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class CommandesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')
                    ->label('N°')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('entreprise.raison_sociale')
                    ->label('Boutique')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('nom_client')
                    ->label('Client')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_client')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('montant_total')
                    ->label('Total')
                    ->money('XOF', locale: 'fr') // Formate automatiquement en FCFA
                    ->sortable(),

                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'en_attente' => 'warning',
                        'valide' => 'info',
                        'livre' => 'success',
                        'annule' => 'danger',
                        default => 'slate',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'en_attente' => '⏰ En attente',
                        'valide' => '✅ Validée',
                        'livre' => '📦 Livrée',
                        'annule' => '❌ Annulée',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Heure')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
                // Filtrer par boutique en un clic
                SelectFilter::make('entreprise_id')
                    ->label('Filtrer par Boutique')
                    ->relationship('entreprise', 'raison_sociale')
                    ->searchable()
                    ->preload()
                    ->native(false),

                // Filtrer par statut de commande
                SelectFilter::make('statut')
                    ->options([
                        'en_attente' => 'En attente',
                        'valide' => 'Validée',
                        'livre' => 'Livrée',
                        'annule' => 'Annulée',
                    ])
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
