# Backend : inscription simplifiée
Dézippe à la racine de Laravel (écrase 2 fichiers) puis : php artisan route:clear && php artisan config:clear
- OnboardingController.php : seuls raison_sociale, contact_whatsapp, email, password sont obligatoires.
  prenom/name/phone restent acceptés (l'ancien formulaire marche toujours). Sans "name" -> nom de la boutique ; sans "phone" -> le WhatsApp.
- GET /api/onboarding/verifier?nom=... : renvoie l'adresse qui sera attribuée (ne crée rien).
- routes/api.php : ATTENTION, c'est la version P2 complète. Si tu l'as modifiée depuis, ajoute seulement la ligne
  Route::get('/onboarding/verifier', [OnboardingController::class, 'verifier'])->middleware('throttle:60,1');
Testé : php -l et la logique des adresses. Laravel lui-même n'a pas pu être lancé ici.
