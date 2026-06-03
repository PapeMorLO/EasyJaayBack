<?php

namespace App\Filament\Admin\Resources\Entreprises;

use App\Filament\Admin\Resources\Entreprises\Pages\CreateEntreprise;
use App\Filament\Admin\Resources\Entreprises\Pages\EditEntreprise;
use App\Filament\Admin\Resources\Entreprises\Pages\ListEntreprises;
use App\Filament\Admin\Resources\Entreprises\Schemas\EntrepriseForm;
use App\Filament\Admin\Resources\Entreprises\Tables\EntreprisesTable;
use App\Models\Entreprise;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EntrepriseResource extends Resource
{
    protected static ?string $model = Entreprise::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | UnitEnum | null $navigationGroup  = 'Paramètres Boutique';
    
    protected static ?string $navigationLabel = 'Ma Boutique';
    
    protected static ?string $pluralLabel = 'Configuration Boutique';

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

     /**
     * ISOLEMENT MULTI-TENANT : Force Filament à ne retourner QUE la boutique de l'utilisateur connecté.
     */
  public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('id', auth()->user()->entreprise_id);
    }

    /**
     * SÉCURITÉ SAAS : Empêche un commerçant de supprimer sa boutique ou d'en créer une autre depuis l'admin.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
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
