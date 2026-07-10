<?php

namespace App\Filament\Superadmin\Resources\Entreprises\Pages;

use App\Filament\Superadmin\Resources\Entreprises\EntrepriseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEntreprise extends EditRecord
{
    protected static string $resource = EntrepriseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
