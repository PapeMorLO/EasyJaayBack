<?php

namespace App\Filament\Admin\Resources\SousCategories\Pages;

use App\Filament\Admin\Resources\SousCategories\SousCategorieResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSousCategories extends ListRecords
{
    protected static string $resource = SousCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
