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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
                // Clés de relations et isolation
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            $table->foreignId('sous_categorie_id')->constrained('sous_categories')->onDelete('cascade');
            
            // Informations produit
            $table->string('reference')->unique()->nullable();
            $table->string('designation');
            $table->text('description')->nullable();
            $table->string('type')->default('physique'); // physique ou service
            
            // Tarification & Stock
            $table->integer('prix_achat')->nullable();
            $table->integer('prix_vente');
            $table->integer('taux_promotion')->default(0); // En pourcentage
            $table->integer('quantite_stock')->default(0);
            
            // Attributs & Médias
            $table->string('color')->nullable();
            $table->string('taille_dimension')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();
            
            // Statut de visibilité
            $table->enum('etat', ['brouillon', 'publie'])->default('publie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
