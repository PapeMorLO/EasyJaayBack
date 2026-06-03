<?php

namespace App\Filament\Admin\Resources\SousCategories\Pages;

use App\Filament\Admin\Resources\SousCategories\SousCategorieResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSousCategorie extends EditRecord
{
    protected static string $resource = SousCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
