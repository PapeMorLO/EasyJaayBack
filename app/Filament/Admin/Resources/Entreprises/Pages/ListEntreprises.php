<?php

namespace App\Filament\Admin\Resources\Entreprises\Pages;

use App\Filament\Admin\Resources\Entreprises\EntrepriseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEntreprises extends ListRecords
{
    protected static string $resource = EntrepriseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
