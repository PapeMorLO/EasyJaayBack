<?php

namespace App\Filament\Superadmin\Resources\Entreprises\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;
use App\Models\Entreprise; 
use Filament\Actions\Action;
use Filament\Actions\BulkAction;

class EntreprisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-store.png')),

                Tables\Columns\TextColumn::make('raison_sociale')
                    ->label('Nom de la Boutique')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('contact_whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-m-phone')
                    ->searchable(),

                // Le bouton magique pour bloquer/débloquer un client depuis la liste
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Statut Actif')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('Visiter')
                    ->icon('heroicon-m-globe-alt')
                    ->color('info')
                    ->url(fn (Entreprise $record): string => url('https://easyjaay.baobapp.tech/' . $record->slug))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('Suspendre')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => false])),
                    DeleteBulkAction::make(),
                    
                ]),
            ]);
    }
}
