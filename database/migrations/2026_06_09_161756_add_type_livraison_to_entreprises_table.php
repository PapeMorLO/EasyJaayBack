<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            // Ajout de la colonne après contact_whatsapp pour la lisibilité
            $table->enum('type_livraison', ['gratuit', 'selon_zone'])
                ->default('selon_zone')
                ->after('contact_whatsapp');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            //
            $table->dropColumn('type_livraison');
        });
    }
};
