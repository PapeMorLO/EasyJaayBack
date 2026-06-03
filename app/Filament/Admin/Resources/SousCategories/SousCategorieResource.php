<?php

namespace App\Filament\Admin\Resources\SousCategories;

use App\Filament\Admin\Resources\SousCategories\Pages\CreateSousCategorie;
use App\Filament\Admin\Resources\SousCategories\Pages\EditSousCategorie;
use App\Filament\Admin\Resources\SousCategories\Pages\ListSousCategories;
use App\Filament\Admin\Resources\SousCategories\Schemas\SousCategorieForm;
use App\Filament\Admin\Resources\SousCategories\Tables\SousCategoriesTable;
use App\Models\SousCategorie;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum; 

class SousCategorieResource extends Resource
{
    protected static ?string $model = SousCategorie::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';
    protected static string|UnitEnum|null $navigationGroup = 'Configuration Catalogue';
    protected static ?string $navigationLabel = 'Sous-catégories';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SousCategorieForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SousCategoriesTable::configure($table);
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
            'index' => ListSousCategories::route('/'),
            'create' => CreateSousCategorie::route('/create'),
            'edit' => EditSousCategorie::route('/{record}/edit'),
        ];
    }
}
