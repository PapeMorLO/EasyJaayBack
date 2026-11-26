# DiayRek — Priorité 2 (backend) : une boutique qui inspire confiance

À dézipper **à la racine du projet Laravel**. Prérequis : le correctif P1 est déjà installé.

## Ce que ça ajoute

| Nouveauté | Où la trouver |
|---|---|
| Page **« Ma boutique »** : présentation, WhatsApp, adresse, paiements acceptés, détails de livraison, réseaux sociaux, Pixel Facebook | Menu de la boutique (en haut à gauche) → « Ma boutique » |
| Widget **« Votre boutique est prête à X/7 »** : liste ce qui manque pour rassurer un inconnu (disparaît quand tout est rempli) | Haut du tableau de bord |
| Bouton **« Partager »** sur chaque produit : ouvre WhatsApp avec le lien du produit (`...?src=whatsapp`) | Liste des produits |
| 2 nouvelles adresses d'API : infos de confiance, et un produit seul | `/api/shops/{slug}/infos` et `/api/shops/{slug}/produits/{id}` |
| Commande `php artisan images:reduire` : allège les anciennes photos lourdes | Terminal |

## Carte des fichiers

```
database/migrations/2026_11_27_100000_priorite2_infos_boutique.php   ← 8 colonnes FACULTATIVES sur "entreprises"
app/Models/Entreprise.php                                            ← fillable + cast "paiements"
app/Http/Controllers/Api/ShopController.php                          ← show + infos + produit
routes/api.php                                                       ← 2 routes ajoutées
app/Filament/Admin/Pages/Tenancy/EditProfilBoutique.php              ← la page "Ma boutique"
app/Filament/Admin/Widgets/ProgressionBoutique.php                   ← la liste "prête à vendre"
resources/views/filament/admin/widgets/progression-boutique.blade.php
app/Filament/Admin/Resources/Produits/Tables/ProduitsTable.php       ← bouton Partager
app/Providers/Filament/AdminPanelProvider.php                        ← +2 lignes (tenantProfile)
app/Console/Commands/ReduireImages.php                               ← images:reduire
```

> `routes/api.php`, `AdminPanelProvider.php`, `ShopController.php` et `ProduitsTable.php` **remplacent** tes versions.
> Si tu as modifié l'un d'eux depuis la P1, fais plutôt le changement à la main :
> - `AdminPanelProvider` : `use App\Filament\Admin\Pages\Tenancy\EditProfilBoutique;` puis `->tenantProfile(EditProfilBoutique::class)` après `->tenantMenu(true)`.
> - `routes/api.php` : `Route::get('/shops/{slug}/infos', [ShopController::class, 'infos']);` et `Route::get('/shops/{slug}/produits/{id}', [ShopController::class, 'produit']);`.

## Commandes

```bash
mysqldump -u root -p ecommerce > sauvegarde-avant-p2.sql
php artisan migrate --pretend      # voir le SQL (8 "add column", rien d'autre)
php artisan migrate
php artisan optimize:clear
```

Les nouvelles colonnes sont vides pour toutes les boutiques existantes : **rien ne change visuellement** tant qu'un vendeur n'a rien rempli.

## Alléger les anciennes photos

Les nouvelles photos sont déjà réduites à l'envoi (P1). Les anciennes pèsent encore 3 à 8 Mo : boutique lente, et l'aperçu WhatsApp d'un lien ne s'affiche pas si l'image est trop lourde.

```bash
cp -r storage/app/public/produits storage/app/public/produits-sauvegarde   # SAUVEGARDE (à supprimer plus tard)
php artisan images:reduire --dry-run     # liste ce qui serait fait, ne change rien
php artisan images:reduire               # réduit vraiment (même nom de fichier, largeur max 1200 px, qualité 82)
```

Le dossier de sauvegarde est dans `storage/app/public` donc public : supprime-le une fois les photos vérifiées.
La commande ne touche pas aux fichiers déjà légers, remet les photos de téléphone dans le bon sens, et écrit via un fichier temporaire (jamais une photo à moitié écrite).

## Optionnel : l'adresse du site Next.js

Le bouton « Partager » utilise `https://easyjaay.baobapp.tech` par défaut. Pour la changer sans toucher au code, ajoute dans `config/app.php` :
`'front_url' => env('FRONT_URL', 'https://easyjaay.baobapp.tech'),`

## Test (10 minutes)

1. Menu de la boutique → **Ma boutique** : remplis tout, enregistre. Mets un numéro avec espaces (`77 123 45 67`) : il est enregistré en `221771234567`. Mets `abc` dans Pixel : refusé.
2. Tableau de bord : le widget passe de 2/7 à 7/7 puis disparaît.
3. `curl https://easyjaayback.baobapp.tech/api/shops/TON-SLUG/infos` → tes infos, **sans** aucune donnée privée (prix d'achat, e-mail...).
4. `curl .../api/shops/TON-SLUG/produits/ID` → le produit ; avec l'ID d'un **brouillon** ou d'une **autre boutique** → 404.
5. Liste des produits → **Partager** : WhatsApp s'ouvre avec le lien (nécessite le front P2).

## Ce qui a été vérifié, et ce qui ne l'a pas été

- Vérifié : syntaxe de tous les fichiers PHP ; la commande `images:reduire` sur de vraies images (3000×2000 → 1200×800, JPEG 2 Mo → 132 Ko, PNG, photo avec orientation EXIF, fichier illisible ignoré, aucun fichier temporaire restant).
- **Non exécuté sur ton Laravel / Filament** : la migration, la page « Ma boutique », le widget et le bouton Partager sont écrits avec les mêmes composants Filament que tes fichiers existants, mais à essayer chez toi. Si une erreur apparaît, envoie-moi le message : il indique le fichier et la ligne.
