<?php

namespace App\Filament\Admin\Resources\Commandes\Pages;

use App\Filament\Admin\Resources\Commandes\CommandeResource;
use Filament\Resources\Pages\ListRecords;

class ListCommandes extends ListRecords
{
    protected static string $resource = CommandeResource::class;

    // Plus de bouton "Créer" : une commande vient du site ou de la caisse.
}
