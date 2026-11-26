# DiayRek — Correctif priorité 1 (backend Laravel + Filament)

À dézipper **à la racine du projet Laravel** (là où se trouve `artisan`), en acceptant de remplacer les fichiers.

## Ce que ça corrige, en une phrase par point

| Problème | Correction |
|---|---|
| Le navigateur envoyait les prix et le total | Laravel relit **tous les prix dans la base** (promotion comprise) |
| Un client pouvait commander (et vider le stock) d'un produit d'une **autre boutique** | Le produit doit appartenir à la boutique et être publié |
| Commande perdue sans erreur visible | Erreurs claires (stock, article retiré), plus de message technique envoyé au client |
| Taille et couleur jamais enregistrées | Elles sont enregistrées et visibles dans la fiche commande |
| Supprimer un produit effaçait l'historique des ventes | Les lignes de commande sont gardées (le nom est copié à l'achat) |
| Le bouton « WhatsApp direct » n'enregistrait rien | Il crée une commande (`source = direct`) avant d'ouvrir WhatsApp |
| Le stock baissait pour des commandes fantômes et n'était jamais restitué | Le stock baisse à la **validation**, et remonte si la commande est annulée |
| API de la boutique : `prix_achat` et brouillons exposés | Seuls les champs publics sont envoyés, uniquement les produits publiés |
| Numéros WhatsApp invalides (4 boutiques sur 7 dans ton dump) | Normalisation `77 123 45 67` → `221771234567`, et refus des numéros invalides |
| Slug en double (500 + message SQL) / slugs réservés (`admin`…) | Slug unique (`-2`, `-3`) et liste de mots réservés |
| Aucune limite d'appels | `throttle` sur l'inscription, les commandes et les compteurs |
| Caisse : la recherche affichait les produits des autres boutiques (`orWhere`) | Parenthèses ajoutées ; produits toujours filtrés par boutique ; prix et stock relus en base à l'encaissement |
| Fiches produit toujours vides (« Aucune description… ») | Champs **description**, prix d'achat, promotion, brouillon, référence dans le formulaire |
| Photos de téléphone de 5 Mo servies telles quelles | Réduites à 1200 px avant l'envoi ; `image1` passe en `text` (plus de limite à ~5 photos) |
| Badge « commandes en attente » compté sur toutes les boutiques | Filtré par boutique |
| `User` n'implémentait pas `FilamentUser` | Corrigé (voir « À vérifier » plus bas) |
| `redirect_url` d'inscription vers une page qui n'existe pas | Pointe vers `/admin/login` |

Nouveautés : notifications dans l'admin (cloche + liste des commandes rafraîchie toutes les 30 s), bouton **WhatsApp** vers le client dans la liste des commandes, filtres par statut et origine, widget **« Parcours des visiteurs »**.

## Carte des fichiers

```
database/migrations/
  2026_11_26_100000_priorite1_commandes_et_suivi.php          ← colonnes, tables evenements + notifications
  2026_11_26_100100_rendre_le_stock_des_commandes_non_validees.php ← remet en stock l'ancien stock "bloqué"
app/Support/Telephone.php                                      ← normalise les numéros (un seul endroit)
app/Models/  Commande.php  LigneCommande.php (nouveau)  Produit.php  User.php
app/Http/Controllers/Api/  CommandeController.php  ShopController.php
                           OnboardingController.php  EvenementController.php (nouveau)
routes/api.php
app/Filament/Admin/Pages/POSCaisse.php
app/Filament/Admin/Resources/Produits/Schemas/ProduitForm.php
app/Filament/Admin/Resources/Commandes/
  CommandeResource.php  Pages/ListCommandes.php  Pages/ViewCommande.php
  Tables/CommandesTable.php  Schemas/CommandeInfolist.php
app/Filament/Admin/Widgets/EntonnoirBoutique.php (nouveau)
app/Providers/Filament/AdminPanelProvider.php                  ← notifications activées
```

## Commandes à lancer

```bash
# 0. SAUVEGARDE de la base (les migrations modifient des tables et remettent du stock)
mysqldump -u root -p ecommerce > sauvegarde-avant-p1.sql

# 1. Dézipper à la racine du projet Laravel (remplacer les fichiers), puis :
php artisan migrate
php artisan optimize:clear
```

Fichiers devenus inutiles, tu peux les supprimer : `Pages/CreateCommande.php`, `Pages/EditCommande.php` et `Schemas/CommandeForm.php` (dans `Resources/Commandes`), ainsi que le modèle `SousCategorie.php` (il pointe vers une colonne qui n'existe plus).

## Trois changements de comportement à connaître

1. **Le stock baisse à la validation, plus à la création.** Une commande « en attente » ne retire plus de stock : un script ou un client qui abandonne ne peut plus vider une boutique. Passer la commande à *Validée* ou *Livrée* retire le stock ; la passer à *Annulée* le remet. La caisse (POS) vend tout de suite, donc elle retire le stock immédiatement, comme avant. La 2ᵉ migration corrige les stocks déjà « bloqués » par des commandes en attente ou annulées.
2. **Les produits en rupture restent visibles** dans la boutique (avec le badge « Épuisé » que ton front gérait déjà). Avant, ils disparaissaient. Pour revenir à l'ancien comportement : dans `ShopController`, ajouter `->where('quantite_stock', '>', 0)` à la requête des produits.
3. **`prix_vente` dans l'API = le prix que le client paie** (promotion appliquée). L'ancien prix est dans `prix_barre` (vide s'il n'y a pas de promotion).

## À vérifier après le déploiement

- **`.env` en production** : `APP_ENV=production` et `APP_DEBUG=false`. Avant ce correctif, ta classe `User` n'implémentait pas `FilamentUser`, ce qui ne fonctionne en production que si `APP_ENV=local`. Si ton serveur tourne en `local`, des erreurs détaillées peuvent être visibles des visiteurs. Maintenant `User` est correct, donc tu peux passer en `production` sans perdre l'accès à l'admin (teste ta connexion juste après).
- **Limites d'appels (`throttle`) derrière nginx / Cloudflare** : si Laravel ne connaît pas l'adresse réelle du visiteur, tous les visiteurs comptent comme UN seul et seraient bloqués ensemble. Dans `bootstrap/app.php`, vérifie la présence de :
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->trustProxies(at: '*');
  })
  ```
- **Mot de passe du super admin** (`contact@diayrek.com`) : change-le, son hash figurait dans le dump envoyé.

## Test rapide (10 minutes)

Remplace l'adresse par celle de ton API.

```bash
# 1. Commande normale (produit 1 de Sokhna : exige taille ET couleur) -> 201 + "numero"
curl -s -X POST https://easyjaayback.baobapp.tech/api/commandes -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"entreprise_slug":"sokhna-cosmetiques","nom_client":"Test","phone_client":"77 123 45 67","adresse_livraison":"Dakar","montant_total":1,"produits":[{"id":1,"quantite":1,"prix_unitaire":1,"selected_size":"42","selected_color":"Noir"}]}'
#    -> le total doit être 5000, PAS 1 (les prix envoyés sont ignorés)

# 2. Sans taille -> 422 "Choisissez une taille..."
# 3. Produit 2 (boutique Djolof) envoyé à Sokhna -> 422 "n'est plus disponible"
# 4. Dans l'admin : passer la commande à "Validée" -> le stock baisse ; à "Annulée" -> il remonte
# 5. Bouton direct :
curl -s -X POST https://easyjaayback.baobapp.tech/api/commandes/direct -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"entreprise_slug":"sokhna-cosmetiques","produits":[{"id":3,"quantite":1}]}'
# 6. https://easyjaayback.baobapp.tech/api/shops/sokhna-cosmetiques  -> aucun champ prix_achat
# 7. Inscription avec le nom "Sokhna Cosmetiques" -> slug sokhna-cosmetiques-2 ; avec le nom "Admin" -> admin-boutique
# 8. Caisse : taper la référence d'un produit d'une autre boutique -> aucun résultat
```

## Lire l'entonnoir par canal (MySQL)

Le widget du tableau de bord montre le total. Pour comparer Facebook, Snapchat, etc. :

```sql
SELECT canal,
       SUM(type = 'visite_boutique') AS visites,
       SUM(type = 'vue_produit')     AS produits_vus,
       SUM(type = 'ajout_panier')    AS ajouts_panier
FROM evenements
WHERE entreprise_id = 1 AND created_at >= NOW() - INTERVAL 30 DAY
GROUP BY canal;

SELECT canal, source, COUNT(*) AS commandes
FROM commandes
WHERE entreprise_id = 1 AND source <> 'pos'
GROUP BY canal, source;
```

Les données n'apparaissent qu'une fois le **front** mis à jour (voir l'autre zip).

## Ce qui a été vérifié, et ce qui ne l'a pas été

- Vérifié : la syntaxe de tous les fichiers PHP (21), et la logique de normalisation des numéros, du prix avec promotion et des slugs (23 vérifications).
- **Non exécuté sur une vraie application Laravel / Filament** : je n'ai pas pu installer Laravel dans mon environnement. Les migrations, les contrôleurs, le changement de statut qui ajuste le stock, la caisse et les écrans Filament (v5) sont donc à essayer chez toi avec la liste ci-dessus. Les écrans Filament sont écrits d'après les versions de tes fichiers ; si une méthode Filament a un nom différent dans ta version, l'erreur t'indiquera le fichier et la ligne.
