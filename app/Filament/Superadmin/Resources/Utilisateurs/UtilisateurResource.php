<?php

namespace App\Filament\Superadmin\Resources\Utilisateurs;

use App\Filament\Superadmin\Resources\Utilisateurs\Pages\CreateUtilisateur;
use App\Filament\Superadmin\Resources\Utilisateurs\Pages\EditUtilisateur;
use App\Filament\Superadmin\Resources\Utilisateurs\Pages\ListUtilisateurs;
use App\Filament\Superadmin\Resources\Utilisateurs\Schemas\UtilisateurForm;
use App\Filament\Superadmin\Resources\Utilisateurs\Tables\UtilisateursTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UtilisateurResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Utilisateurs & Marchands';
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';

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
