<?php

namespace App\Filament\Admin\Resources\Commandes;

use App\Filament\Admin\Resources\Commandes\Pages\ListCommandes;
use App\Filament\Admin\Resources\Commandes\Pages\ViewCommande;
use App\Filament\Admin\Resources\Commandes\Schemas\CommandeInfolist;
use App\Filament\Admin\Resources\Commandes\Tables\CommandesTable;
use App\Models\Commande;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CommandeResource extends Resource
{
    protected static ?string $model = Commande::class;

    protected static string|BackedEnum|null $navigationIcon =  'heroicon-o-shopping-cart';
    protected static string|UnitEnum|null $navigationGroup = 'Gestion Commerciale';
    protected static ?string $navigationLabel = 'Commandes';
    protected static ?int $navigationSort = 2;

    // Les commandes arrivent du site ou de la caisse : le vendeur ne les crée pas à la main
    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommandeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommandesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // Badge avec le nombre de commandes en attente.
    // getEloquentQuery() applique le filtre de la boutique : chaque vendeur ne compte QUE les siennes.
    // (Avant : getModel()::where(...) comptait les commandes de toutes les boutiques.)
    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('statut', 'en_attente')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger'; // Rouge pour attirer l'attention du commerçant
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommandes::route('/'),
            'view' => ViewCommande::route('/{record}'),
        ];
    }
}
