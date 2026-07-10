<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::firstOrCreate(
            ['email' => 'admin@diayrek.com'],
            [
                'entreprise_id' => null, // Le super admin n'est pas lié à un tenant
                'name' => 'Système',
                'prenom' => 'Administrateur',
                'phone' => '778762197',
                'password' => Hash::make('pass123'),
                'role' => 'superadmin', // Clé d'accès au panel
                'adresse' => 'Dakar, Sénégal' // Ajout du champ manquant
            ]
        );
    }
}
