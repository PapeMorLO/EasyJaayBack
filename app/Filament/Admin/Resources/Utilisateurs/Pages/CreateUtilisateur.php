<?php

namespace App\Filament\Admin\Resources\Utilisateurs\Pages;

use App\Filament\Admin\Resources\Utilisateurs\UtilisateurResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUtilisateur extends CreateRecord
{
    protected static string $resource = UtilisateurResource::class;
}
