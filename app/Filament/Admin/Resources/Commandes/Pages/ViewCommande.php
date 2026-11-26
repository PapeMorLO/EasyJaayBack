<?php

namespace App\Filament\Admin\Resources\Commandes\Pages;

use App\Filament\Admin\Resources\Commandes\CommandeResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCommande extends ViewRecord
{
    protected static string $resource = CommandeResource::class;

    // Plus de bouton "Modifier" : le statut se change directement dans la liste.
}
