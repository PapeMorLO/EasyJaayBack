<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Entreprise;
use App\Models\User;
use App\Models\Categorie;
use App\Models\SousCategorie;
use App\Models\Produit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        /*
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        // 1. Boutique A : Sokhna Cosmétiques
        $sokhna = Entreprise::create([
            'raison_sociale' => 'Sokhna Cosmétiques',
            'slug' => 'sokhna-cosmetiques',
            'couleur_theme' => '#ec4899', // Rose
            'contact_call' => '221771111111',
            'contact_whatsapp' => '221771111111',
            'is_active' => true,
        ]);

        $userSokhna = User::create([
            'entreprise_id' => $sokhna->id,
            'name' => 'LO',
            'prenom' => 'Pape Mor',
            'email' => 'papemorlo@hotmail.fr',
            'phone' => '771111111',
            'password' => Hash::make('pass'),
            'role' => 'admin',
        ]);

        // Catalogue Sokhna
        $catCosme = Categorie::create([
            'entreprise_id' => $sokhna->id,
            'designation' => 'Maquillage',
        ]);
        $subCatLevres = SousCategorie::create([
            'entreprise_id' => $sokhna->id,
            'categorie_id' => $catCosme->id,
            'designation' => 'Rouge à lèvres',
        ]);
        Produit::create([
            'entreprise_id' => $sokhna->id,
            'sous_categorie_id' => $subCatLevres->id,
            'designation' => 'Gloss Repulpant',
            'prix_vente' => 5000,
            'quantite_stock' => 15,
            'etat' => 'publie',
        ]);

        // 2. Boutique B : Djolof Tech
        $djolof = Entreprise::create([
            'raison_sociale' => 'Djolof Tech',
            'slug' => 'djolof-tech',
            'couleur_theme' => '#10b981', // Vert
            'contact_call' => '221772222222',
            'contact_whatsapp' => '221772222222',
            'is_active' => true,
        ]);

        User::create([
            'entreprise_id' => $djolof->id,
            'name' => 'Ahmadou',
            'prenom' => 'Ndiaye',
            'email' => 'ahmadou@test.com',
            'phone' => '772222222',
            'password' => Hash::make('pass'),
            'role' => 'admin',
        ]);

        // Catalogue Djolof Tech
        $catTech = Categorie::create([
            'entreprise_id' => $djolof->id,
            'designation' => 'Accessoires',
        ]);
        $subCatChargeur = SousCategorie::create([
            'entreprise_id' => $djolof->id,
            'categorie_id' => $catTech->id,
            'designation' => 'Chargeurs',
        ]);
        Produit::create([
            'entreprise_id' => $djolof->id,
            'sous_categorie_id' => $subCatChargeur->id,
            'designation' => 'Powerbank 20k mAh',
            'prix_vente' => 15000,
            'quantite_stock' => 8,
            'etat' => 'publie',
        ]);

        $this->call([
            //SyscohadaSeeder::class,
        ]);
    }
}
