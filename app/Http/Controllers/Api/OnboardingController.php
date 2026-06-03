<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Entreprise;
use App\Models\User;

class OnboardingController extends Controller
{
    //
    public function register(Request $request)
    {
        // 1. Validation stricte et rapide
        $request->validate([
            'raison_sociale' => 'required|string|max:255|unique:entreprises,raison_sociale',
            //'prenom' => 'required|string|max:255',
            'name' => 'required|string|max:255', // Nom de famille
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'contact_whatsapp' => 'required|string',
        ]);

        try {
            // 2. Transaction Database pour éviter les comptes fantômes en cas de crash
            $result = DB::transaction(function () use ($request) {
                
                // Génération automatique du slug (ex: "Sokhna Cosmétiques" -> "sokhna-cosmetiques")
                $slug = Str::slug($request->raison_sociale);

                // Création du Tenant (L'entreprise)
                $entreprise = Entreprise::create([
                    'raison_sociale' => $request->raison_sociale,
                    'slug' => $slug,
                    'contact_call' => $request->phone,
                    'contact_whatsapp' => $request->contact_whatsapp,
                    'couleur_theme' => '#10b981', // Vert émeraude par défaut (Fiabilité)
                    'is_active' => true,
                ]);

                // Création de l'administrateur de la boutique
                $user = User::create([
                    'entreprise_id' => $entreprise->id,
                    'prenom' => $request->prenom,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'role' => 'admin', // Gérant principal
                ]);

                return $entreprise;
            });

            // 3. Réponse Instantanée pour Next.js avec l'URL cible de Filament v5
            return response()->json([
                'status' => 'success',
                'message' => 'Boutique créée avec succès !',
                'redirect_url' => url("/admin/{$result->slug}/login"),
                'data' => [
                    'shop_slug' => $result->slug,
                    'raison_sociale' => $result->raison_sociale
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Échec de la création de la boutique.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}