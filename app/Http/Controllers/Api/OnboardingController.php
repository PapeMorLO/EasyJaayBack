<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use App\Models\User;
use App\Support\Telephone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    // Ces mots sont déjà utilisés par ton site (routes Laravel / Filament / Next.js) :
    // une boutique ne peut pas s'appeler comme ça.
    private const SLUGS_RESERVES = [
        'admin', 'api', 'login', 'logout', 'register', 'inscription', 'storage', 'commandes',
        'produits', 'categories', 'boutique', 'boutiques', 'dashboard', 'images', 'static',
        'public', 'support', 'aide', 'contact', 'diayrek', 'easyjaay', 'app', 'www', 'test',
    ];

    public function register(Request $request)
    {
        // 1. On normalise les numéros AVANT de valider : "77 123 45 67" devient "221771234567".
        //    Le nouveau formulaire ne demande que le WhatsApp : sans "phone", on réutilise le WhatsApp
        //    (le vendeur pourra donner un autre numéro d'appel plus tard dans "Ma boutique").
        $whatsapp = Telephone::normaliser($request->input('contact_whatsapp'));
        $request->merge([
            'contact_whatsapp' => $whatsapp,
            'phone'            => Telephone::normaliser($request->input('phone')) ?: $whatsapp,
        ]);

        // 2. Validation
        $request->validate([
            'raison_sociale'   => 'required|string|min:2|max:255|unique:entreprises,raison_sociale',
            'prenom'           => 'nullable|string|max:255',
            'name'             => 'nullable|string|max:255', // Nom de famille (facultatif : sinon le nom de la boutique)
            'email'            => 'required|email|max:255|unique:users,email',
            'phone'            => ['required', 'regex:' . Telephone::REGEX, 'unique:users,phone'],
            'contact_whatsapp' => ['required', 'regex:' . Telephone::REGEX],
            'password'         => 'required|string|min:6',
        ], [
            'phone.regex'            => 'Numéro invalide. Exemple : 77 123 45 67',
            'contact_whatsapp.regex' => 'Numéro WhatsApp invalide. Exemple : 77 123 45 67',
            'phone.unique'           => 'Ce numéro est déjà utilisé.',
            'email.unique'           => 'Cet e-mail est déjà utilisé.',
            'raison_sociale.unique'  => 'Ce nom de boutique existe déjà.',
        ]);

        try {
            // 3. Transaction : pas de boutique sans administrateur, ni l'inverse
            $entreprise = DB::transaction(function () use ($request) {

                $entreprise = Entreprise::create([
                    'raison_sociale'   => $request->raison_sociale,
                    'slug'             => $this->slugDisponible($request->raison_sociale),
                    'contact_call'     => $request->phone,
                    'contact_whatsapp' => $request->contact_whatsapp,
                    'couleur_theme'    => '#10b981', // Vert émeraude par défaut
                    'is_active'        => true,
                ]);

                User::create([
                    'entreprise_id' => $entreprise->id,
                    'prenom'        => $request->prenom ?? '',
                    'name'          => $request->name ?: $request->raison_sociale,
                    'email'         => $request->email,
                    'phone'         => $request->phone,
                    'password'      => Hash::make($request->password),
                    'role'          => 'admin', // Gérant principal
                ]);

                return $entreprise;
            });

            return response()->json([
                'status'       => 'success',
                'message'      => 'Boutique créée avec succès !',
                // La page de connexion de Filament est /admin/login (elle ne contient pas le slug)
                'redirect_url' => url('/admin/login'),
                'data' => [
                    'shop_slug'      => $entreprise->slug,
                    'raison_sociale' => $entreprise->raison_sociale,
                ]
            ], 201);

        } catch (\Throwable $e) {
            // Le détail va dans les logs, jamais au visiteur
            Log::error('Inscription impossible : ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => "Échec de la création de la boutique. Réessayez dans un instant.",
            ], 500);
        }
    }

    /**
     * Appelé par le formulaire d'inscription pendant que le vendeur tape le nom de sa boutique :
     * "Quelle adresse aura ma boutique ? Le nom est-il déjà pris ?"
     * Ne crée rien. Limité à 60 appels par minute (voir routes/api.php).
     */
    public function verifier(Request $request)
    {
        $nom = trim((string) $request->query('nom', ''));

        if (mb_strlen($nom) < 2) {
            return response()->json(['slug' => null, 'nom_pris' => false, 'adresse_modifiee' => false]);
        }

        $slug = $this->slugDisponible($nom);

        return response()->json([
            'slug'             => $slug,                                                    // l'adresse qui sera attribuée
            'nom_pris'         => Entreprise::where('raison_sociale', $nom)->exists(),      // le nom est déjà une boutique
            'adresse_modifiee' => $slug !== $this->slugDeBase($nom),                        // "-2", "-boutique"... ajouté
        ]);
    }

    // "Sokhna Cosmétiques" -> "sokhna-cosmetiques". Trop court ou réservé ("admin") : on ajoute "-boutique".
    private function slugDeBase(string $nom): string
    {
        $base = Str::slug($nom);

        if ($base === '') {
            return 'boutique';
        }
        if (strlen($base) < 3 || in_array($base, self::SLUGS_RESERVES, true)) {
            return $base . '-boutique';
        }

        return $base;
    }

    /**
     * Crée un slug libre : "sokhna-cosmetiques", sinon "sokhna-cosmetiques-2", "-3", ...
     */
    private function slugDisponible(string $nom): string
    {
        $base = $this->slugDeBase($nom);
        $slug = $base;
        $numero = 2;

        while (Entreprise::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $numero;
            $numero++;
        }

        return $slug;
    }
}
