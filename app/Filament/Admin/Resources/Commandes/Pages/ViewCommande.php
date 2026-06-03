<?php

namespace App\Filament\Admin\Resources\Commandes\Pages;

use App\Filament\Admin\Resources\Commandes\CommandeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCommande extends ViewRecord
{
    protected static string $resource = CommandeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
