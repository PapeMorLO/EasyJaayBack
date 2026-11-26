<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\Tenancy\EditProfilBoutique;
use App\Models\Produit;
use App\Support\Telephone;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

/**
 * "Votre boutique est prête à vendre ?"
 * Liste ce qui manque pour qu'un inconnu ait confiance. Disparaît quand tout est rempli.
 */
class ProgressionBoutique extends Widget
{
    protected string $view = 'filament.admin.widgets.progression-boutique';

    protected static ?int $sort = -2;   // tout en haut, avant le partage

    protected int | string | array $columnSpan = 'full';

    // Le widget n'apparaît que s'il reste quelque chose à faire
    public static function canView(): bool
    {
        return collect(self::etapes())->contains(fn ($etape) => ! $etape['ok']);
    }

    protected function getViewData(): array
    {
        $etapes = self::etapes();

        return [
            'etapes'   => $etapes,
            'faites'   => collect($etapes)->where('ok', true)->count(),
            'total'    => count($etapes),
            'lienProfil' => EditProfilBoutique::getUrl(),
        ];
    }

    private static function etapes(): array
    {
        $boutique = Filament::getTenant();
        if (! $boutique) {
            return [];
        }

        $produits = Produit::where('entreprise_id', $boutique->id)->where('etat', 'publie');
        $nbProduits = (clone $produits)->count();
        $sansDescription = (clone $produits)->where(function ($q) {
            $q->whereNull('description')->orWhere('description', '');
        })->count();

        return [
            [
                'ok'      => $nbProduits >= 3,
                'titre'   => 'Au moins 3 produits publiés',
                'conseil' => 'Une boutique avec 1 ou 2 articles paraît vide.',
            ],
            [
                'ok'      => $nbProduits > 0 && $sansDescription === 0,
                'titre'   => 'Une description sur chaque produit',
                'conseil' => $sansDescription > 0 ? "{$sansDescription} produit(s) sans description." : 'Ajoutez des produits d\'abord.',
            ],
            [
                'ok'      => Telephone::estValide(Telephone::normaliser($boutique->contact_whatsapp)),
                'titre'   => 'Numéro WhatsApp valide',
                'conseil' => 'Sans numéro valide, vous ne recevez pas les commandes.',
            ],
            [
                'ok'      => filled($boutique->adresse),
                'titre'   => 'Adresse ou lieu de retrait',
                'conseil' => 'Le client veut savoir où vous trouver.',
            ],
            [
                'ok'      => ! empty($boutique->paiements),
                'titre'   => 'Moyens de paiement',
                'conseil' => 'Wave, Orange Money, espèces... dites-le avant qu\'on vous le demande.',
            ],
            [
                'ok'      => filled($boutique->infos_livraison),
                'titre'   => 'Prix et délais de livraison',
                'conseil' => 'Dakar, banlieue, régions : combien et en combien de temps ?',
            ],
            [
                'ok'      => filled($boutique->facebook_url) || filled($boutique->instagram_url)
                             || filled($boutique->snapchat_url) || filled($boutique->tiktok_url),
                'titre'   => 'Un réseau social',
                'conseil' => 'Un compte avec de vraies photos prouve que vous existez.',
            ],
        ];
    }
}
