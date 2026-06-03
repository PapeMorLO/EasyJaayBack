<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Compte; 

class SyscohadaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $comptes = [
            // Classe 4 : Comptes de tiers (Clients, Fournisseurs, État)
            ['numero' => '401100', 'intitule' => 'Fournisseurs d\'exploitation locaux', 'classe' => 4],
            ['numero' => '411100', 'intitule' => 'Clients locaux', 'classe' => 4],
            ['numero' => '443100', 'intitule' => 'TVA facturée sur ventes', 'classe' => 4],
            ['numero' => '445100', 'intitule' => 'TVA récupérable sur achats', 'classe' => 4],
            ['numero' => '447100', 'intitule' => 'État, BRS (Retenue à la source 5%)', 'classe' => 4],

            // Classe 5 : Comptes de Trésorerie (Banques, Caisses, Mobile Money)
            ['numero' => '521100', 'intitule' => 'Banques locales (CBAO, SGBS, etc.)', 'classe' => 5],
            ['numero' => '571100', 'intitule' => 'Caisse principale Principale', 'classe' => 5],
            ['numero' => '571200', 'intitule' => 'Caisse Mobile Money (Wave / Orange Money)', 'classe' => 5],

            // Classe 6 : Comptes de charges (Dépenses, Achats)
            ['numero' => '601100', 'intitule' => 'Achats de marchandises', 'classe' => 6],
            ['numero' => '604100', 'intitule' => 'Achats d\'études et prestations de services', 'classe' => 6],
            ['numero' => '611100', 'intitule' => 'Transports de marchandises', 'classe' => 6],
            ['numero' => '624100', 'intitule' => 'Frais de téléphone et internet', 'classe' => 6],
            ['numero' => '632100', 'intitule' => 'Impôts et taxes directs', 'classe' => 6],
            ['numero' => '661100', 'intitule' => 'Rémunérations du personnel (Salaires)', 'classe' => 6],

            // Classe 7 : Comptes de produits (Revenus, Ventes)
            ['numero' => '701100', 'intitule' => 'Ventes de marchandises', 'classe' => 7],
            ['numero' => '706100', 'intitule' => 'Prestations de services réalisées', 'classe' => 7],
        ];

        foreach ($comptes ?? [] as $compte) {
            Compte::updateOrCreate(['numero' => $compte['numero']], $compte);
        }
    }
}
