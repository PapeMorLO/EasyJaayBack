<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    // 1. Ajouter la hiérarchie dans la table categories
    if (!Schema::hasColumn('categories', 'parent_id')) {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')
                  ->constrained('categories')->nullOnDelete();
        });
    

        // 2. Créer la table pivot entre produits et catégories
        Schema::create('categorie_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->foreignId('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Supprimer l'ancienne contrainte et colonne sur la table produits
        Schema::table('produits', function (Blueprint $table) {
            // Note : Adaptez le nom de la clé étrangère selon votre convention actuelle
            $table->dropForeign(['sous_categorie_id']);
            $table->dropColumn('sous_categorie_id');
        });
    }
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->foreignId('sous_categorie_id')->nullable()->constrained('sous_categories');
        });

        Schema::dropIfExists('categorie_produit');

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};