<?php

namespace App\Filament\Admin\Pages\Tenancy;

use App\Support\Telephone;
use Filament\Forms;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Page "Ma boutique" (menu de la boutique en haut à gauche).
 * C'est ici que le vendeur remplit ce qui rassure ses clients.
 * Le slug (l'adresse de la boutique) n'est PAS modifiable : changer le slug casserait tous ses liens partagés.
 */
class EditProfilBoutique extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Ma boutique';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Présentation')
                ->description('Ce que le client lit en arrivant sur votre boutique.')
                ->schema([
                    Forms\Components\TextInput::make('raison_sociale')
                        ->label('Nom de la boutique')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\ColorPicker::make('couleur_theme')
                        ->label('Couleur de la boutique'),

                    Forms\Components\Textarea::make('a_propos')
                        ->label('Qui êtes-vous ? (2 phrases)')
                        ->rows(3)
                        ->maxLength(400)
                        ->placeholder('Ex : Boutique de cosmétiques naturels aux Parcelles Assainies, ouverte depuis 2021. Produits vérifiés, échange possible sous 48 h.')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Où et comment vous joindre')
                ->description('Un client qui hésite veut savoir qu\'il y a une vraie personne et un vrai lieu derrière.')
                ->schema([
                    Forms\Components\TextInput::make('contact_whatsapp')
                        ->label('Numéro WhatsApp (celui qui reçoit les commandes)')
                        ->required()
                        ->tel()
                        ->helperText('Format : 77 123 45 67. Le 221 est ajouté automatiquement.')
                        ->dehydrateStateUsing(fn ($state) => Telephone::normaliser($state))
                        ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                            if (! Telephone::estValide(Telephone::normaliser($value))) {
                                $fail('Numéro invalide : il faut 9 chiffres (ex : 77 123 45 67).');
                            }
                        }),

                    Forms\Components\TextInput::make('contact_call')
                        ->label('Numéro pour les appels')
                        ->required()
                        ->tel()
                        ->dehydrateStateUsing(fn ($state) => Telephone::normaliser($state)),

                    Forms\Components\TextInput::make('adresse')
                        ->label('Adresse ou lieu de retrait')
                        ->placeholder('Ex : Parcelles Assainies, unité 15, en face de la pharmacie')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Livraison et paiement')
                ->schema([
                    Forms\Components\Select::make('type_livraison')
                        ->label('Livraison')
                        ->options([
                            'gratuit'    => 'Gratuite',
                            'selon_zone' => 'Selon la zone',
                        ])
                        ->required(),

                    Forms\Components\CheckboxList::make('paiements')
                        ->label('Moyens de paiement acceptés')
                        ->helperText('Affichés en haut de la boutique, dans le panier et sur chaque produit. Le client paie directement le vendeur.')
                        ->options([
                            'wave'            => 'Wave',
                            'orange_money'    => 'Orange Money',
                            'free_money'      => 'Free Money',
                            'cash_livraison'  => 'Espèces à la livraison',
                            'cash_boutique'   => 'Espèces en boutique',
                        ])
                        ->columns(2),

                    Forms\Components\Textarea::make('infos_livraison')
                        ->label('Détails de livraison')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Ex : Dakar 1 000 F, banlieue 1 500 F, régions 3 000 F. Livraison en 24 à 48 h. Retrait gratuit en boutique.')
                        ->helperText('Ce texte apparaît en haut de votre boutique, dans le panier et sur chaque produit. Mettez l\'essentiel au début : en haut, seul le début est visible.')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Réseaux sociaux')
                ->description('Facultatif. Un compte avec de vrais abonnés et de vraies photos rassure beaucoup.')
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('facebook_url')->label('Page Facebook')->url()->placeholder('https://facebook.com/...'),
                    Forms\Components\TextInput::make('instagram_url')->label('Instagram')->url()->placeholder('https://instagram.com/...'),
                    Forms\Components\TextInput::make('snapchat_url')->label('Snapchat')->url()->placeholder('https://snapchat.com/add/...'),
                    Forms\Components\TextInput::make('tiktok_url')->label('TikTok')->url()->placeholder('https://tiktok.com/@...'),
                ])->columns(2),

            Section::make('Publicité Facebook (avancé)')
                ->description('Si vous faites de la publicité sur Facebook ou Instagram, le Pixel permet à Facebook de trouver des gens qui achètent vraiment.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('pixel_facebook')
                        ->label('Identifiant du Pixel')
                        ->placeholder('Ex : 123456789012345')
                        ->helperText('Dans le Gestionnaire d\'événements Meta : le numéro (chiffres seulement) à côté du nom du Pixel.')
                        ->regex('/^\d{8,20}$/')
                        ->validationMessages(['regex' => 'Le Pixel est un numéro : chiffres seulement (8 à 20).']),
                ]),
        ]);
    }
}
