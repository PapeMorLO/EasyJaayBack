<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;

class PartageBoutiqueWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.partage-boutique-widget';

    // Placement prioritaire en haut du tableau de bord
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $tenant = Filament::getTenant() ?? auth()->user()?->entreprise;

        if (! $tenant) {
            return ['has_shop' => false];
        }

        // Adresse du site Next.js. Pour la changer sans toucher au code :
        // ajouter 'front_url' => env('FRONT_URL', '...') dans config/app.php
        $domain = config('app.front_url', 'https://easyjaay.baobapp.tech');
        $shopUrl = rtrim($domain, '/') . '/' . $tenant->slug;

        // Le paramètre ?src= dit à la boutique d'où vient le visiteur
        // (visible ensuite dans "Parcours des visiteurs" et dans la colonne Canal des commandes)
        $lienWhatsapp = $shopUrl . '?src=whatsapp';
        $lienQr = $shopUrl . '?src=qr';

        // On n'annonce la livraison que si la boutique l'offre vraiment
        $ligneLivraison = $tenant->type_livraison === 'gratuit'
            ? "\n\nLivraison gratuite ! 📦"
            : '';

        $messageText = "✨ Bonjour ! Découvrez ma boutique en ligne *{$tenant->raison_sociale}*.\n\n"
            . "👉 Cliquez sur ce lien pour voir mes articles et commander facilement sur WhatsApp :\n"
            . "🔗 {$lienWhatsapp}"
            . $ligneLivraison;

        return [
            'has_shop' => true,
            'raison_sociale' => $tenant->raison_sociale,
            'shop_url' => $shopUrl,
            'whatsapp_share_url' => 'https://api.whatsapp.com/send?text=' . urlencode($messageText),
            'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($lienQr),
        ];
    }
}
