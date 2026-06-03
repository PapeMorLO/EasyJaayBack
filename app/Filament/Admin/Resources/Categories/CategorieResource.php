<?php

namespace App\Filament\Admin\Resources\Categories;

use App\Filament\Admin\Resources\Categories\Pages\CreateCategorie;
use App\Filament\Admin\Resources\Categories\Pages\EditCategorie;
use App\Filament\Admin\Resources\Categories\Pages\ListCategories;
use App\Filament\Admin\Resources\Categories\Schemas\CategorieForm;
use App\Filament\Admin\Resources\Categories\Tables\CategoriesTable;
use App\Models\Categorie;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategorieResource extends Resource
{
    protected static ?string $model = Categorie::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static string|UnitEnum|null $navigationGroup = 'Configuration Catalogue';
    protected static ?string $navigationLabel = 'Catégories';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CategorieForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategorie::route('/create'),
            'edit' => EditCategorie::route('/{record}/edit'),
        ];
    }
}
