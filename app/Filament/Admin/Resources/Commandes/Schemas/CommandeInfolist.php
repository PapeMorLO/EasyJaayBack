<?php

namespace App\Filament\Admin\Resources\Commandes\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommandeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Commande')
                    ->schema([
                        TextEntry::make('id')->label('Numéro'),
                        TextEntry::make('created_at')->label('Date')->dateTime(),
                        TextEntry::make('montant_total')->label('Total')->money('XOF', locale: 'fr'),
                        TextEntry::make('statut')
                            ->label('Statut')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => match ($state) {
                                'en_attente' => 'En attente',
                                'valide'     => 'Validée',
                                'livre'      => 'Livrée',
                                'annule'     => 'Annulée',
                                default      => (string) $state,
                            }),
                        TextEntry::make('source')
                            ->label('Origine')
                            ->formatStateUsing(fn (?string $state) => match ($state) {
                                'direct' => 'WhatsApp direct',
                                'pos'    => 'Caisse',
                                default  => 'Site',
                            }),
                        TextEntry::make('canal')->label('Canal')->placeholder('-'),
                    ])->columns(3),

                Section::make('Détails du client')
                    ->schema([
                        TextEntry::make('nom_client')->label('Nom complet'),
                        TextEntry::make('phone_client')->label('Téléphone'),
                        TextEntry::make('adresse_livraison')->label('Adresse de livraison'),
                    ])->columns(3),

                Section::make('Articles commandés')
                    ->schema([
                        // "lignes" garde le nom, la taille et la couleur choisis par le client
                        RepeatableEntry::make('lignes')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('nom')->label('Produit'),
                                TextEntry::make('taille')->label('Taille')->placeholder('-'),
                                TextEntry::make('couleur')->label('Couleur')->placeholder('-'),
                                TextEntry::make('quantite')->label('Quantité'),
                                TextEntry::make('prix_unitaire')->money('XOF', locale: 'fr')->label('Prix U.'),
                            ])->columns(5),
                    ]),
            ]);
    }
}
