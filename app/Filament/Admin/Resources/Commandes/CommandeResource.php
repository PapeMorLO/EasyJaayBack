<?php

namespace App\Filament\Admin\Resources\Commandes;

use App\Filament\Admin\Resources\Commandes\Pages\CreateCommande;
use App\Filament\Admin\Resources\Commandes\Pages\EditCommande;
use App\Filament\Admin\Resources\Commandes\Pages\ListCommandes;
use App\Filament\Admin\Resources\Commandes\Pages\ViewCommande;
use App\Filament\Admin\Resources\Commandes\Schemas\CommandeForm;
use App\Filament\Admin\Resources\Commandes\Schemas\CommandeInfolist;
use App\Filament\Admin\Resources\Commandes\Tables\CommandesTable;
use App\Models\Commande;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum; 

class CommandeResource extends Resource
{
    protected static ?string $model = Commande::class;

    protected static string|BackedEnum|null $navigationIcon =  'heroicon-o-shopping-cart';
    protected static string|UnitEnum|null $navigationGroup = 'Gestion Commerciale';    
    protected static ?string $navigationLabel = 'Commandes';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CommandeForm::configure($schema);
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

        // Bonus Filament v5 : Affiche un badge rouge avec le nombre de commandes en attente
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('statut', 'en_attente')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger'; // Rouge pour attirer l'attention du commerçant
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommandes::route('/'),
            'create' => CreateCommande::route('/create'),
            'view' => ViewCommande::route('/{record}'),
            'edit' => EditCommande::route('/{record}/edit'),
        ];
    }
}
