<?php

namespace App\Filament\Superadmin\Resources\Utilisateurs\Pages;

use App\Filament\Superadmin\Resources\Utilisateurs\UtilisateurResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUtilisateur extends EditRecord
{
    protected static string $resource = UtilisateurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
