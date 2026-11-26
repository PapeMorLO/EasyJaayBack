# Correctif widgets du tableau de bord

À dézipper à la racine du projet Laravel (remplace 4 fichiers dans `app/Filament/Admin/Widgets/`).

```bash
php artisan optimize:clear
```

| Fichier | Changement |
|---|---|
| EntonnoirBoutique | Ordre d'affichage (3) ; carte renommée « Visiteurs (30 jours) » pour ne plus ressembler à celle de StatsBoutique |
| StatsBoutique | Chaque requête filtre sur la boutique connectée ; alerte stock = produits physiques publiés ; carte renommée « Visites depuis le début » |
| GraphiqueVentes | Filtre sur la boutique connectée |
| PartageBoutiqueWidget | Lien `?src=whatsapp` / `?src=qr` ; phrase sur la livraison seulement si la livraison est gratuite ; adresse du site lue dans `config('app.front_url')` (valeur par défaut = ton adresse actuelle, rien à configurer) |

La vue Blade `partage-boutique-widget` n'est pas touchée : les clés envoyées (`shop_url`, `whatsapp_share_url`, `qr_code_url`, `raison_sociale`, `has_shop`) sont les mêmes.

## Test
1. Connecte-toi avec deux boutiques différentes : les revenus et le graphique doivent être différents.
2. Clique sur « partager sur WhatsApp » : le lien envoyé doit finir par `?src=whatsapp`.
3. Ouvre ce lien depuis un autre téléphone, puis regarde « Visiteurs (30 jours) » : il doit monter de 1 (si le front P1 est déployé).
