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
        //
            // Dans la migration de la table des entreprises / shops
        Schema::table('entreprises', function (Blueprint $table) {
            $table->unsignedBigInteger('visites')->default(0);
        });

        // Dans la migration de la table des produits
        Schema::table('produits', function (Blueprint $table) {
            $table->unsignedBigInteger('visites')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('entreprises', function (Blueprint $table) {
            //
            $table->dropColumn('type_livraison');
        });

         Schema::table('produits', function (Blueprint $table) {
            //
            $table->dropColumn('visites');
        });
    }
};
