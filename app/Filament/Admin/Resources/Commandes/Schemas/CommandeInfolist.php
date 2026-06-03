<?php

namespace App\Filament\Admin\Resources\Commandes\Schemas;

use Filament\Schemas\Schema;

class CommandeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                \Filament\Infolists\Components\Section::make('Détails du Client')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('nom_client')->label('Nom complet'),
                        \Filament\Infolists\Components\TextEntry::make('phone_client')->label('Téléphone'),
                        \Filament\Infolists\Components\TextEntry::make('adresse_livraison')->label('Adresse de livraison'),
                    ])->columns(3),
                
                \Filament\Infolists\Components\Section::make('Articles commandés')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('produits')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('designation')->label('Produit'),
                                \Filament\Infolists\Components\TextEntry::make('pivot.quantite')->label('Quantité'),
                                \Filament\Infolists\Components\TextEntry::make('pivot.prix_unitaire')->money('XOF')->label('Prix U.'),
                            ])->columns(3)
                    ])
            ]);
    }
}
