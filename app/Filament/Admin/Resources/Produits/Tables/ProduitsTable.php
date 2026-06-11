<?php

namespace App\Filament\Admin\Resources\Produits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class ProduitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('image1')->label('Images')
                    ->disk('public'),
                    //->circular() // Affiche des avatars ronds élégants
                    //->stacked()  // Empile les images les unes sur les autres s'il y en a plusieurs
                    //->limit(3)   // Limite l'affichage à 3 vignettes max dans le tableau pour rester propre
                    //->limitedRemainingText(),

                Tables\Columns\TextColumn::make('designation')->searchable(),
                Tables\Columns\TextColumn::make('prix_vente')->sortable()->suffix(' FCFA'),
                Tables\Columns\TextColumn::make('quantite_stock')->sortable(),
                Tables\Columns\TextColumn::make('visites')
                ->label('Consultations')
                ->icon('heroicon-o-eye')
                ->numeric()
                ->sortable() // Permet au commerçant de cliquer pour trier du plus vu au moins vu !
                ->color('gray'),
            ])
            ->defaultSort('visites', 'desc')
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
