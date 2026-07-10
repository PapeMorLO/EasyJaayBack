<?php

namespace App\Filament\Superadmin\Resources\Entreprises;

use App\Filament\Superadmin\Resources\Entreprises\Pages\CreateEntreprise;
use App\Filament\Superadmin\Resources\Entreprises\Pages\EditEntreprise;
use App\Filament\Superadmin\Resources\Entreprises\Pages\ListEntreprises;
use App\Filament\Superadmin\Resources\Entreprises\Schemas\EntrepriseForm;
use App\Filament\Superadmin\Resources\Entreprises\Tables\EntreprisesTable;
use App\Models\Entreprise;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EntrepriseResource extends Resource
{
    protected static ?string $model = Entreprise::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    // Le nom affiché dans le menu
    protected static ?string $navigationLabel = 'Boutiques';
    protected static ?string $modelLabel = 'Boutique';
    protected static ?string $pluralModelLabel = 'Boutiques';

    protected static ?string $recordTitleAttribute = 'no';

    public static function form(Schema $schema): Schema
    {
        return EntrepriseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntreprisesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntreprises::route('/'),
            'create' => CreateEntreprise::route('/create'),
            'edit' => EditEntreprise::route('/{record}/edit'),
        ];
    }
}
