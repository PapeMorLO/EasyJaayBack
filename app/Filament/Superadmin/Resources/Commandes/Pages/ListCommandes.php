<?php

namespace App\Filament\Superadmin\Resources\Commandes\Pages;

use App\Filament\Superadmin\Resources\Commandes\CommandeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommandes extends ListRecords
{
    protected static string $resource = CommandeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
