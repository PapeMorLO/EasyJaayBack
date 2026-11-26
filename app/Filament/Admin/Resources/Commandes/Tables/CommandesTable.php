<?php

namespace App\Filament\Admin\Resources\Commandes\Tables;

use App\Models\Commande;
use App\Support\Telephone;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommandesTable
{
    private const STATUTS = [
        'en_attente' => 'En attente',
        'valide'     => 'Validée',
        'livre'      => 'Livrée',
        'annule'     => 'Annulée',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('N°')->sortable(),
                Tables\Columns\TextColumn::make('nom_client')->searchable()->label('Client'),
                Tables\Columns\TextColumn::make('phone_client')->label('Téléphone'),

                // D'où vient la commande ? "WhatsApp direct" = le client a cliqué mais n'a pas rempli de
                // formulaire : le vendeur doit attendre son message pour confirmer.
                Tables\Columns\TextColumn::make('source')
                    ->label('Origine')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'direct' => 'WhatsApp direct',
                        'pos'    => 'Caisse',
                        default  => 'Site',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'direct' => 'warning',
                        'pos'    => 'gray',
                        default  => 'success',
                    }),

                Tables\Columns\TextColumn::make('canal')
                    ->label('Canal')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('montant_total')
                    ->money('XOF', locale: 'fr')
                    ->sortable()
                    ->label('Total'),

                Tables\Columns\SelectColumn::make('statut')
                    ->options(self::STATUTS)
                    ->selectablePlaceholder(false)
                    ->label('Statut'),

                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable()->label('Date'),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')   // la liste se rafraîchit toute seule : les nouvelles commandes apparaissent
            ->filters([
                SelectFilter::make('statut')->label('Statut')->options(self::STATUTS),
                SelectFilter::make('source')->label('Origine')->options([
                    'web'    => 'Site',
                    'direct' => 'WhatsApp direct',
                    'pos'    => 'Caisse',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),

                // Ouvre WhatsApp vers le client avec un message de confirmation déjà écrit
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (Commande $record) => Telephone::estValide(Telephone::normaliser($record->phone_client)))
                    ->url(fn (Commande $record) => self::lienWhatsapp($record), shouldOpenInNewTab: true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function lienWhatsapp(Commande $commande): string
    {
        $numero = Telephone::normaliser($commande->phone_client);
        $boutique = $commande->entreprise?->raison_sociale ?? 'notre boutique';
        $montant = number_format($commande->montant_total, 0, ',', ' ');

        $message = "Bonjour {$commande->nom_client}, c'est {$boutique}. "
                 . "Merci pour votre commande n°{$commande->id} ({$montant} FCFA). "
                 . "Je vous confirme sa disponibilité et nous organisons la livraison.";

        return 'https://wa.me/' . $numero . '?text=' . rawurlencode($message);
    }
}
