<?php

namespace App\Filament\Admin\Resources\Commandes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables; 

class CommandesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')->label('N°')->sortable(),
                Tables\Columns\TextColumn::make('nom_client')->searchable()->label('Client'),
                Tables\Columns\TextColumn::make('phone_client')->label('Téléphone'),
                Tables\Columns\TextColumn::make('montant_total')
                    ->money('XOF', locale: 'fr')
                    ->sortable()
                    ->label('Total'),
                Tables\Columns\SelectColumn::make('statut')
                    ->options([
                        'en_attente' => 'En attente',
                        'valide' => 'Validée',
                        'livre' => 'Livrée',
                        'annule' => 'Annulée',
                    ])
                    ->label('Statut'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable()->label('Date'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                //EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
