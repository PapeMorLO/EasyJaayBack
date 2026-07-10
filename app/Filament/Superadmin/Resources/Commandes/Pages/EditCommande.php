<?php

namespace App\Filament\Superadmin\Resources\Commandes\Pages;

use App\Filament\Superadmin\Resources\Commandes\CommandeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCommande extends EditRecord
{
    protected static string $resource = CommandeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
