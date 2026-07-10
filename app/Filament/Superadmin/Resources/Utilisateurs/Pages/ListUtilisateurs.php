<?php

namespace App\Filament\Superadmin\Resources\Utilisateurs\Pages;

use App\Filament\Superadmin\Resources\Utilisateurs\UtilisateurResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUtilisateurs extends ListRecords
{
    protected static string $resource = UtilisateurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
