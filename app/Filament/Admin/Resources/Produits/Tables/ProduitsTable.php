<?php

namespace App\Filament\Admin\Resources\Produits\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;

class ProduitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image1')->label('Images')
                    ->disk('public'),

                Tables\Columns\TextColumn::make('designation')->searchable(),
                Tables\Columns\TextColumn::make('prix_vente')->sortable()->suffix(' FCFA'),
                Tables\Columns\TextColumn::make('quantite_stock')->sortable(),
                Tables\Columns\TextColumn::make('visites')
                    ->label('Consultations')
                    ->icon('heroicon-o-eye')
                    ->numeric()
                    ->sortable() // Permet au commerçant de trier du plus vu au moins vu
                    ->color('gray'),
            ])
            ->defaultSort('visites', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                // Ouvre WhatsApp avec le lien de CE produit : il s'affiche avec sa photo et son prix
                // quand on le colle dans une conversation, un statut ou une publicité.
                Action::make('partager')
                    ->label('Partager')
                    ->icon('heroicon-o-share')
                    ->color('success')
                    ->visible(fn ($record) => $record->etat === 'publie')
                    ->url(function ($record) {
                        $front = rtrim(config('app.front_url', 'https://easyjaay.baobapp.tech'), '/');
                        $lien = $front . '/' . Filament::getTenant()->slug . '/p/' . $record->id . '?src=whatsapp';
                        $texte = "{$record->designation}\n{$lien}";

                        return 'https://api.whatsapp.com/send?text=' . urlencode($texte);
                    })
                    ->openUrlInNewTab(),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
