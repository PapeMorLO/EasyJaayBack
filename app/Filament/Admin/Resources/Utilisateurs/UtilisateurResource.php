<?php

namespace App\Filament\Admin\Resources\Utilisateurs;

use App\Filament\Admin\Resources\Utilisateurs\Pages\CreateUtilisateur;
use App\Filament\Admin\Resources\Utilisateurs\Pages\EditUtilisateur;
use App\Filament\Admin\Resources\Utilisateurs\Pages\ListUtilisateurs;
use App\Filament\Admin\Resources\Utilisateurs\Schemas\UtilisateurForm;
use App\Filament\Admin\Resources\Utilisateurs\Tables\UtilisateursTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum; 

class UtilisateurResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|UnitEnum|null $navigationGroup = 'Paramètres Boutique';
    protected static ?string $navigationLabel = 'Équipe / Vendeurs';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'Utilisateurs';

    public static function form(Schema $schema): Schema
    {
        return UtilisateurForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtilisateursTable::configure($table);
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
            'index' => ListUtilisateurs::route('/'),
            'create' => CreateUtilisateur::route('/create'),
            'edit' => EditUtilisateur::route('/{record}/edit'),
        ];
    }
}
