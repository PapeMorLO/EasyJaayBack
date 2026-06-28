<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;

class PartageBoutiqueWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.partage-boutique-widget';

     // Placement prioritaire en haut du tableau de bord
    protected static ?int $sort = -1;

    // Le widget s'étend sur toute la largeur de l'écran pour être bien visible
    protected int | string | array $columnSpan = 'full';

    /**
     * Récupère les données dynamiques de la boutique du commerçant connecté.
     */
    protected function getViewData(): array
    {
        $tenant = Filament::getTenant();

        // Si l'architecture n'utilise pas le multi-tenant natif de Filament,
        // on récupère l'entreprise liée à l'utilisateur connecté.
        if (!$tenant) {
            $tenant = auth()->user()->entreprise; 
        }

        if (!$tenant) {
            return [
                'has_shop' => false
            ];
        }

        // URL publique de la boutique sur votre front-end Next.js
        // Remplacez par votre nom de domaine de production si nécessaire (ex: https://diayrek.com)
        $domain = 'https://easyjaay.baobapp.tech'; 
        $shopUrl = rtrim($domain, '/') . '/' . $tenant->slug;

        // Message pré-rempli pour WhatsApp (encodé pour URL)
        $messageText = "✨ Bonjour ! Découvrez ma boutique en ligne sur *{$tenant->raison_sociale}*.\n\n"
            . "👉 Cliquez sur ce lien pour parcourir nos articles et commander facilement sur WhatsApp :\n"
            . "🔗 {$shopUrl}\n\n"
            . "Livraison rapide disponible ! 📦";

        $whatsappShareUrl = "https://api.whatsapp.com/send?text=" . urlencode($messageText);

        // Génération automatique d'un QR code haute définition gratuit
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($shopUrl);

        return [
            'has_shop' => true,
            'raison_sociale' => $tenant->raison_sociale,
            'shop_url' => $shopUrl,
            'whatsapp_share_url' => $whatsappShareUrl,
            'qr_code_url' => $qrCodeUrl,
        ];
    }
}
